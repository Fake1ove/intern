<?php
//include '_connectTest.php';
include '_connect.php';
$requestMethod = $_SERVER["REQUEST_METHOD"];
function runGet($stmt)
{
	foreach ($stmt as $row) {
		$output[] = $row;
	}
	if (!empty($output)) {
		$json= json_encode($output);
		return $json;
	}

}

function runNotGet($stmt)
{
	if ($stmt) {
		echo json_encode(['status' => 'ok', 'message' => 'Update Data Complete']);
	} else {
		echo json_encode(['status' => 'error', 'message' => 'Error']);
	}
}

//ตรวจสอบหากใช้ Method GET
if ($requestMethod == 'GET') {

}
if ($requestMethod == 'POST') {
	//ตรวจสอบการส่งค่า id
	if (isset($_GET['id']) && !empty($_GET['id'])) {
		//*--------Select--------*//
		if ($_GET['id'] == 1) {

			$token = $_POST['token'];
			if ($token == 'epyTtnemyaPegnahCipa') {
				$stmt = $conn->prepare("SELECT * from intra_tl.tlpayment_type ;");
				$stmt->execute();

				

				// $stmt = runGet($stmt);

				while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
					echo 'รหัส : "' . $row['tlpayment_type_id'] . "\"\n";
					echo 'ชื่อ : "' . $row['tlpayment_type'] . "\"\n";
				
				}
				
				
			}
			else {
				echo ('No Data');
			}
		} else if ($_GET['id'] == 2) {

		} else if ($_GET['id'] == 3) {

		}
	} else {
		
		}
}
if ($requestMethod == 'PUT') {
}
if ($requestMethod == 'DELETE') {
}


?>