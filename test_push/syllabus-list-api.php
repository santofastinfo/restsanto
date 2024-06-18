<?php
$std_id = $_GET['std_id'];
include('../include/conn.php');
$limit = 10;
if (isset($_GET["page"])) {
   $current_page  = $_GET["page"];
} else {
   $current_page = 1;
};
$start_from = ($current_page - 1) * $limit;

### 186 (SPEECF) ### running_service ## tbl 
$sql = "SELECT ads.service_id,ads.days,ads.syllabus_topic,sc.syllabus_topic_status
          FROM add_syllabus ads
          LEFT JOIN syllabus_completed sc ON sc.syllabus_id = ads.id
          LEFT JOIN running_service rs ON rs.id = ads.service_id
          LEFT JOIN student_registration sr ON sr.std_course=rs.course_code
          WHERE sr.std_id =".$std_id."
           ORDER BY ads.id ASC
          LIMIT $start_from, $limit";
$query = $conn->query($sql);

$data1 = [];
while ($row = $query->fetch_assoc()) {
   if ($row['syllabus_topic_status'] == 1) { ## complete
      $status = 'Complete';
   } else if ($row['syllabus_topic_status'] == 2) { ## continue
      $status = 'Continue';
   } else { ## not started  
      $status = 'Not Started';
   }

   $days = ucwords($row['days']);
   $syllabus_topic = ucwords($row['syllabus_topic']);
   $data1[] = [
      'status' => $status,
      'days' => $days,
      'syllabus_topic' => $syllabus_topic
   ];
}

$sql = "SELECT COUNT(*) AS total FROM add_syllabus WHERE service_id = 186";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$total_records = $row['total'];
$data = array('syllabus_list' => $data1, 'total_records' => $total_records);

echo json_encode($data);
