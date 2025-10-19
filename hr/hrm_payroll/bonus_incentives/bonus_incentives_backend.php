<?php
header('Content-Type: application/json');
$resp = ['success'=>false,'message'=>'Unknown error'];

require_once __DIR__ . '/../../db_connector.php';
if(!$conn){
  echo json_encode(['success'=>false, 'message'=>'Database unavailable']);
  exit;
}

$action = isset($_POST['action']) ? $_POST['action'] : '';
if($action === 'fetch'){
  $employee_input = isset($_POST['employee_id']) ? trim($_POST['employee_id']) : '';
  if($employee_input === ''){ echo json_encode(['success'=>false,'message'=>'Missing employee_id']); exit; }

  // Determine whether input is numeric id or staff_id
  if(ctype_digit($employee_input)){
    // Try to fetch base_salary if column exists; fall back if not
    $sqlWithBase = "SELECT id, CONCAT(first_name, ' ', last_name) AS full_name, base_salary FROM ceo WHERE id = ? LIMIT 1";
    if($stmt = @$conn->prepare($sqlWithBase)){
      $intId = intval($employee_input);
      $stmt->bind_param('i', $intId);
      $stmt->execute();
      $res = $stmt->get_result();
      if($row = $res->fetch_assoc()){
        $data = ['full_name'=>$row['full_name'],'id'=>$row['id']];
        if(isset($row['base_salary'])) $data['base_salary'] = $row['base_salary'];
        echo json_encode(['success'=>true, 'data'=>$data]);
      } else {
        echo json_encode(['success'=>false, 'message'=>'Employee not found']);
      }
      $stmt->close();
    } else {
      // fallback without base_salary
      $sql = "SELECT id, CONCAT(first_name, ' ', last_name) AS full_name FROM ceo WHERE id = ? LIMIT 1";
      if($stmt = $conn->prepare($sql)){
        $intId = intval($employee_input);
        $stmt->bind_param('i', $intId);
        $stmt->execute();
        $res = $stmt->get_result();
        if($row = $res->fetch_assoc()){
          echo json_encode(['success'=>true, 'data'=>['full_name'=>$row['full_name'],'id'=>$row['id']]]);
        } else {
          echo json_encode(['success'=>false, 'message'=>'Employee not found']);
        }
        $stmt->close();
      } else {
        echo json_encode(['success'=>false, 'message'=>'Query prepare failed']);
      }
    }
  } else {
    // treat as staff_id string
    $sqlWithBase = "SELECT id, CONCAT(first_name, ' ', last_name) AS full_name, base_salary FROM ceo WHERE staff_id = ? LIMIT 1";
    if($stmt = @$conn->prepare($sqlWithBase)){
      $stmt->bind_param('s', $employee_input);
      $stmt->execute();
      $res = $stmt->get_result();
      if($row = $res->fetch_assoc()){
        $data = ['full_name'=>$row['full_name'],'id'=>$row['id']];
        if(isset($row['base_salary'])) $data['base_salary'] = $row['base_salary'];
        echo json_encode(['success'=>true, 'data'=>$data]);
      } else {
        // fallback: try staff table by staff_id
        $fallbackSql = "SELECT staff_id, CONCAT(first_name, ' ', last_name) AS full_name, base_salary FROM staff WHERE staff_id = ? LIMIT 1";
        if($fs = $conn->prepare($fallbackSql)){
          $fs->bind_param('s', $employee_input);
          $fs->execute();
          $fr = $fs->get_result();
          if($frow = $fr->fetch_assoc()){
            $data = ['full_name'=>$frow['full_name'],'staff_id'=>$frow['staff_id']];
            if(isset($frow['base_salary'])) $data['base_salary'] = $frow['base_salary'];
            echo json_encode(['success'=>true, 'data'=>$data]);
          } else {
            echo json_encode(['success'=>false, 'message'=>'Employee not found (by staff_id)']);
          }
          $fs->close();
        } else {
          echo json_encode(['success'=>false, 'message'=>'Employee not found (by staff_id)']);
        }
      }
      $stmt->close();
    } else {
      // fallback without base_salary
      $sql = "SELECT id, CONCAT(first_name, ' ', last_name) AS full_name FROM ceo WHERE staff_id = ? LIMIT 1";
      if($stmt = $conn->prepare($sql)){
        $stmt->bind_param('s', $employee_input);
        $stmt->execute();
        $res = $stmt->get_result();
        if($row = $res->fetch_assoc()){
          echo json_encode(['success'=>true, 'data'=>['full_name'=>$row['full_name'],'id'=>$row['id']]]);
        } else {
          echo json_encode(['success'=>false, 'message'=>'Employee not found (by staff_id)']);
        }
        $stmt->close();
      } else {
        echo json_encode(['success'=>false, 'message'=>'Query prepare failed']);
      }
    }
  }
  exit;
}

if($action === 'holidays'){
  $rows = [];
  $sql = "SELECT id, name, date, percentage FROM holidays ORDER BY date DESC";
  if($stmt = $conn->prepare($sql)){
    $stmt->execute();
    $res = $stmt->get_result();
    while($r = $res->fetch_assoc()){
      $rows[] = $r;
    }
    echo json_encode(['success'=>true,'data'=>$rows]);
    $stmt->close();
  } else {
    echo json_encode(['success'=>false,'message'=>'Unable to read holidays']);
  }
  exit;
}

if($action === 'save'){
  $employee_input = isset($_POST['employee_id']) ? trim($_POST['employee_id']) : '';
  $type = $_POST['type'] ?? 'Bonus';
  $amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0.00;
  $description = $_POST['description'] ?? '';
  if($employee_input === ''){ echo json_encode(['success'=>false,'message'=>'Missing employee_id']); exit; }

  // resolve staff_id string; if numeric input, try to find staff_id by ceo.id
  $staff_id = null;
  if(ctype_digit($employee_input)){
    $q = "SELECT staff_id FROM ceo WHERE id = ? LIMIT 1";
    if($s = $conn->prepare($q)){
      $intId = intval($employee_input);
      $s->bind_param('i', $intId);
      $s->execute();
      $r = $s->get_result();
      if($row = $r->fetch_assoc()){
        $staff_id = $row['staff_id'];
      }
      $s->close();
    }
  } else {
    // assume input is staff_id
    $staff_id = $employee_input;
  }

  if(!$staff_id){ echo json_encode(['success'=>false,'message'=>'Unable to resolve staff_id']); exit; }

  // Insert using HR-style schema (staff_id, type, amount, created_at)
  $insertSql = "INSERT INTO bonuses_incentives (staff_id, type, amount, created_at) VALUES (?, ?, ?, NOW())";
  if($stmt = $conn->prepare($insertSql)){
    $stmt->bind_param('ssd', $staff_id, $type, $amount);
    if($stmt->execute()){
      echo json_encode(['success'=>true,'message'=>'Saved']);
    } else {
      echo json_encode(['success'=>false,'message'=>'Execute failed: '.$stmt->error]);
    }
    $stmt->close();
  } else {
    echo json_encode(['success'=>false,'message'=>'Prepare failed: '.$conn->error]);
  }
  exit;
}

echo json_encode($resp);

?>
