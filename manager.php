<?php

$config = include 'config.php';

function contacts( $query='' ){
  $contacts = file_get_contents( 'data/contacts.json' );
  $contacts = json_decode($contacts, TRUE);
  $results = [];
  
  usort($contacts, function($a, $b) {
    if($a['status']==$b['status']) return 0;
    return $a['status'] < $b['status']?-1:1;
  });

  if (empty($query)) return $contacts;

  foreach( $contacts as $item ){

    if( is_array( $item ) ){
      if( array_filter($item, function($var) use ($query) { return ( !is_array( $var ) )? stristr( $var, $query ): false; } ) ){
        $results[] = $item;
        continue;
      }
    }
  }
  return $results;
}

function generate_uuid() {
    return sprintf( '%04x%04x%04x',
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
    );
}

if (!empty($_GET['act'])) {
  switch ($_GET['act']) {
    case 'login':
      if (!empty($_POST['pass']) && $_POST['pass'] == "admin123") {
        setcookie('b533dade', '1df8279147d64f18f4d394c5546aec05', time() + (86400 * 30), "/");
      }
    break;
    case 'add':
      $defaults = [
        'id' => generate_uuid(),
        'name' => '',
        'category' => '',
        'phone' => '',
        'address' => '',
        'email' => '',
        'website' => '',
        'description' => '',
        'services' => '',
        'rating' => 0,
        'status' => FALSE,
      ];
      foreach ($defaults as $k => $v) {
        if (!empty($_POST[$k])) {
          if ($k == 'status') boolval($_POST[$k]);
          $defaults[$k] = $_POST[$k];
        }
      }
      $contacts = contacts();
      $contacts[] = $defaults;
      file_put_contents( 'data/contacts.json', json_encode( $contacts ) );
      exit('ok');
    break;
    case 'editsave':
      $id = $_POST['id']; 
      $contact = contacts($id);
      $contact = reset($contact);
      unset($_POST['id']);
      foreach ($_POST as $k => $v) {
        if ( $_POST[$k] != $contact[$k] ) {
          $contact[$k] = $_POST[$k];
        }
      }
      $contacts = contacts();
      if (!empty($contacts)) {
        $temp = [];
        foreach ($contacts as $con) {
          if ( $con['id'] == $id ) {
            $temp[] = $contact;
          }else{
            $temp[] = $con;
          }
        }
        file_put_contents( 'data/contacts.json', json_encode( $temp ) );
      }
      exit('ok');
    break;
    case 'delete':
      $contacts = contacts();
      if (!empty($contacts)) {
        $temp = [];
        foreach ($contacts as $contact) {
          if ($contact['id'] != $_POST['id']) {
            $temp[] = $contact;
          }
        }

        file_put_contents( 'data/contacts.json', json_encode( $temp ) );
      }

      exit('ok');
    break;
    case 'view':
      $contact = contacts($_POST['id']);
      $contact = reset($contact);
      $html = '<ul class="list-group list-group-flush">';
      $values = [
        'id' => 'Idenfikator',
        'name' => 'Nomi',
        'category' => 'Bo‘lim',
        'phone' => 'Telefon raqam',
        'address' => 'Manzil',
        'email' => 'E-mail',
        'website' => 'veb-sayt',
        'description' => 'Tasnif',
        'services' => 'Xizmatlar',
        'rating' => 'Reyting',
      ];

      foreach ($values as $k => $v) {
        $value = ( !empty( $contact[ $k ] ) ) ? $contact[ $k ] : '<span class="badge bg-warning">kiritilmagan</span>';
        $html .= '<li class="list-group-item"><strong>'.$v.':</strong> '.$value.'</li>';
      }
      $html .= '</ul>';
      echo $html;
      exit(1);
    break;

    case 'edit':
      $contact = contacts($_POST['id']);
      $contact = reset($contact);
      $html = '<form class="editform" method="POST" action="?act=editsave" autocomplete="off"><input type="hidden" name="id" value="'.$_POST['id'].'">';
      
      $html .= '<div class="mb-3">';
        $html .= '<label for="name" class="form-label">Kontakt nomi</label><input type="text" class="form-control" name="name" id="name" placeholder="" value="'.$contact['name'].'" required></div>';
      
      $html .= '<div class="mb-3"><label for="category" class="form-label">Bo‘lim</label><select name="category" class="form-select" required>';
        foreach ($config['categories'] as $cat) {
          $html .= '<option value="'.$cat.'" '.( ($contact['category'] == $cat) ? 'selected' : '' ).'>'.$cat.'</option>';
        }
      $html .= '</select></div>';
      
      $html .= '<div class="mb-3"><label for="phone" class="form-label">Telefon raqam</label><input type="text" class="form-control" name="phone" id="phone" placeholder="" value="'.$contact['phone'].'"></div>';
      $html .= '<div class="mb-3"><label for="address" class="form-label">Manzil</label><input type="text" class="form-control" name="address" id="address" placeholder="" value="'.$contact['address'].'"></div>';
      $html .= '<div class="mb-3"><label for="email" class="form-label">E-mail</label><input type="email" class="form-control" name="email" id="email" placeholder="" value="'.$contact['email'].'"></div>';
      $html .= '<div class="mb-3"><label for="website" class="form-label">Veb-site</label><input type="url" class="form-control" name="website" id="website" placeholder="" value="'.$contact['website'].'"></div>';
      $html .= '<div class="mb-3"><label for="services" class="form-label">Xizmatlar</label><input type="text" class="form-control" name="services" id="services" placeholder="" value="'.$contact['services'].'"></div>';
      $html .= '<div class="mb-3"><label for="description" class="form-label">Tasnif</label><textarea class="form-control" name="description" id="description" rows="3">'.$contact['description'].'</textarea></div>';
      $html .= '<div class="mb-3"><label class="form-label">Bo‘lim</label><select name="status" class="form-select"><option value="1" '.( ($contact['status'] == 1) ? 'selected' : '' ).'>Tasdiqlangan</option><option value="0" '.( ($contact['status'] == 0) ? 'selected' : '' ).'>Tasdiqlanmagan</option></select></div>';
      $html .= '<div class="d-grid gap-2"><button class="btn btn-primary" type="submit">Kiritish</button></div>';
      $html .= '</form>';
      echo $html;
      exit(1);
    break;
  }
}

if(isset($_COOKIE['b533dade']) && $_COOKIE['b533dade'] == '1df8279147d64f18f4d394c5546aec05') {
  $contacts = contacts( ( (!empty($_GET['q'])) ? $_GET['q'] : '' ) );
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <title>Aloqa robot Manager</title>
  </head>
  <body class="p-4">
    <ul class="nav nav-tabs" id="myTab" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="contacts-tab" data-bs-toggle="tab" data-bs-target="#contacts" type="button" role="tab" aria-controls="contacts" aria-selected="false">Kontaktlar</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="addcontacts-tab" data-bs-toggle="tab" data-bs-target="#addcontacts" type="button" role="tab" aria-controls="addcontacts" aria-selected="false">Kontakt qo'shish</button>
      </li>
    </ul>

    <div class="tab-content">
      <div class="tab-pane active" id="contacts" role="tabpanel" aria-labelledby="contacts-tab">
        <form action="?search" method="GET" autocomplete="off">
          <div class="mb-3">
            <label class="form-label"></label>
            <input type="search" name="q" class="form-control" placeholder="Izlash..." />
          </div>
        </form>
          <?php
            //['#', 'Nomi', 'Bo‘lim', 'Telefon raqam', 'Manzil', 'Email', 'Veb-sayt', 'Tasnif', 'Xizmatlar', 'Ish vaqti', 'Holat', 'Harakat']
            if (!empty($contacts)) {
              echo '<table class="table table-striped table-bordered mt-3">';
              echo "<tr class=\"text-center\"><th>" . implode("</th><th>", ['Nomi', 'Bo‘lim', 'Telefon raqam', 'Email', 'Veb-sayt', 'Holat', 'Harakat']) . "</th></tr>";
              
              $page = ! empty( $_GET['page'] ) ? (int) $_GET['page'] : 1;
              $page_size = 25;
              $total_records = count($contacts);
              $total_pages   = ceil($total_records / $page_size);
              if ($page > $total_pages) $page = $total_pages;
              if ($page < 1) $page = 1;
              $offset = ($page - 1) * $page_size;
              $data = array_slice($contacts, $offset, $page_size);

              foreach ($data as $contact) {
          ?>
                <tr class="text-center">
                  <td><?php echo $contact['name'];?></td>
                  <td><?php echo $contact['category'];?></td>
                  <td><?php echo $contact['phone'];?></td>
                  <td><?php echo (!empty($contact['email'])) ? $contact['email'] : '';?></td>
                  <td><?php echo (!empty($contact['website'])) ? $contact['website'] : '';?></td>
                  <td><?php echo $contact['status'] ? '<span class="badge bg-success">tasdiqlangan</span>' : '<span class="badge bg-danger">tasdiqlanmagan</span>';?></td>
                  <td>
                    <button type="button" view="<?php echo $contact['id'];?>" class="btn btn-dark btn-sm" title="Batafsil">&#9869;</button>
                    <button type="button" edit="<?php echo $contact['id'];?>" class="btn btn-success btn-sm" title="Tahrirlash">&#9859;</button>
                    <button type="button" delete="<?php echo $contact['id'];?>" class="btn btn-danger btn-sm text-white" title="O'chirish">&nbsp;&#215;&nbsp;</button>
                  </td>
                </tr>
          <?php
              }
              echo '</table><nav><ul class="pagination justify-content-center">';
              $N = min($total_pages, 9);
              $pages_links = [];
              $tmp = $N;
              if ($tmp < $page || $page > $N) $tmp = 2;
              for ($i = 1; $i <= $tmp; $i++) $pages_links[$i] = $i;
              if ($page > $N && $page <= ($total_pages - $N + 2)) {
                for ($i = $page - 3; $i <= $page + 3; $i++) {
                  if ($i > 0 && $i < $total_pages) {
                    $pages_links[$i] = $i;
                  }
                }
              }
              $tmp = $total_pages - $N + 1;
              if ($tmp > $page - 2) $tmp = $total_pages - 1;
              for ($i = $tmp; $i <= $total_pages; $i++) {
                if ($i > 0) {
                  $pages_links[$i] = $i;
                }
              }
              $prev = 1;
              if (count($pages_links) > 1) {
                
                foreach ($pages_links as $p) {
                  $cur = $_GET['page'];
                  $_GET['page'] = ($cur+1);
                  if (($p - $prev-1) > 1) {
                    echo '<li class="page-item"><a class="page-link" href="?'.http_build_query($_GET).'">'.($cur+1).'</a></li>';
                    echo '<li class="page-item"><a class="page-link" href="">...</a></li>';
                  }
                  $prev = $p; 
                  $_GET['page'] = $p;
                  echo '<li class="page-item '.(($p == $page) ? 'active' : '').'"><a class="page-link" href="?'.http_build_query($_GET).'">'.$p.'</a></li>';
                }
              }
              
              echo '</ul></nav>';
            }else{
              echo "";
            }
          ?>
      </div>
      <div class="tab-pane" id="addcontacts" role="tabpanel" aria-labelledby="addcontacts-tab">
        <form class="addform" method="POST" action="?act=add" autocomplete="off">
          <div class="mb-3">
            <label for="name" class="form-label">Kontakt nomi</label>
            <input type="text" class="form-control" name="name" id="name" placeholder="" required>
          </div>
          <div class="mb-3">
            <label for="category" class="form-label">Bo'lim</label>
            <select name="category" class="form-select" required>
              <?php
                  foreach ($config['categories'] as $cat) {
                    echo '<option value="'.$cat.'">'.$cat.'</option>';
                  }
              ?>            
            </select>
          </div>
          <div class="mb-3">
            <label for="phone" class="form-label">Telefon raqam</label>
            <input type="text" class="form-control" name="phone" id="phone" placeholder="">
          </div>
          <div class="mb-3">
            <label for="address" class="form-label">Manzil</label>
            <input type="text" class="form-control" name="address" id="address" placeholder="">
          </div>
          <div class="mb-3">
            <label for="email" class="form-label">E-mail</label>
            <input type="email" class="form-control" name="email" id="email" placeholder="">
          </div>
          <div class="mb-3">
            <label for="website" class="form-label">Veb-site</label>
            <input type="url" class="form-control" name="website" id="website" placeholder="">
          </div>
          <div class="mb-3">
            <label for="services" class="form-label">Xizmatlar</label>
            <input type="text" class="form-control" name="services" id="services" placeholder="">
          </div>
          <div class="mb-3">
            <label for="description" class="form-label">Tasnif</label>
            <textarea class="form-control" name="description" id="description" rows="3"></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Bo'lim</label>
            <select name="status" class="form-select">
              <option value="1">Tasdiqlangan</option>
              <option value="0">Tasdiqlanmagan</option>      
            </select>
          </div>
          <div class="d-grid gap-2">
            <button class="btn btn-primary" type="submit">Kiritish</button>
          </div>
        </form>
      </div>
    </div>

    <script type="text/javascript" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    
    <script type="text/javascript">
      $(document).on('click', '[view]', function(event) {
        event.preventDefault();
        var id = $(this).attr('view');
        $.post('?act=view', {id: id}, function(data, textStatus, xhr) {
          $("#my_modal .modal-body").html(data);
          $("#my_modal .modal-title").html('Batafsil ma\'lumot');
          $("#my_modal").modal("show");
        });
      });
      $(document).on('click', '[delete]', function(event) {
        event.preventDefault();
        var id = $(this).attr('delete');
        if (confirm("Siz chindan ham ushbu ma'lumotni o'chirmoqchimisiz?") == true) {
          $.post('?act=delete', {id: id}, function(data, textStatus, xhr) {
            location.reload();
          });
        }
      });
      $(document).on('click', '[edit]', function(event) {
        event.preventDefault();
        var id = $(this).attr('edit');
        $.post('?act=edit', {id: id}, function(data, textStatus, xhr) {
          $("#my_modal .modal-body").html(data);
          $("#my_modal .modal-title").html('Tahrirlash');
          $("#my_modal").modal("show");
        });
      });
      $(document).on('submit', '.editform', function(event) {
        event.preventDefault();
        var post_data = $(this).serializeArray();
        $.post('?act=editsave', post_data, function(data, textStatus, xhr) {
          location.reload();
        });
      });
      $(document).on('submit', '.addform', function(event) {
        event.preventDefault();
        var post_data = $(this).serializeArray();
        $.post('?act=add', post_data, function(data, textStatus, xhr) {
          location.reload();
        });
      });
    </script>
    <!-- Modal -->
    <div class="modal fade" id="my_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel"></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Yopish"></button>
          </div>
          <div class="modal-body">
            
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Yopish</button>
          </div>
        </div>
      </div>
    </div>

  </body>
</html>
<?php
}else{
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Aloqa robot Manager</title>
</head>
<body>

<script type="text/javascript" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript">
  let person = prompt("Iltimos parolni kiriting", "");

  if (person != null) {
    $.post('?act=login', {pass: person}, function(data, textStatus, xhr) {
      location.reload();
    });
  }else{
    document.write('Ruxsat etilmadi');
  }
</script>
</body>
</html>

<?php
}

?>