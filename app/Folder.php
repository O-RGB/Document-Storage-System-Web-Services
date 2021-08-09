
<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;



//  1. สร้าง Folder *ทุกคน
//  1.1 เพิ่ม Admin Folder |อาจจะรวมกันได้
//  1.2 เพิ่ม User  Folder |อาจจะรวมกันได้
//  1.4 ลบ Folder 
//  1.4 เปลี่ยนชื่อ Folder 
//  1.5 select Folder


// สร้าง Folder
$app->post('/folder/create', function (Request $request, Response $response, $args) {
    $body = $request->getBody();
    $bodyArr = json_decode($body, true);
    $conn = $GLOBALS['dbconn'];
    $stmt = $conn->prepare( "INSERT into folder (Fname,FK_Userid,data) VALUES(?,?,?)");
    $stmt->bind_param("sis",$bodyArr['Fname'],$bodyArr['FK_Userid'],$bodyArr['date']);
    $stmt->execute();

    $stmt = $conn->prepare( "SELECT Folder_ID FROM folder where Fname = ? and FK_Userid = ?");
    $stmt->bind_param("si",$bodyArr['Fname'],$bodyArr['FK_Userid']);
    $stmt->execute();
    $result = $stmt->get_result();
    $newFolder = $result->fetch_assoc();
    mkdir(__DIR__."/../Files/".$newFolder['Folder_ID']);

    $result = $stmt->affected_rows;
    $response->getBody()->write($result."");
    return $response->withHeader('Content-Type','application/json')
                    ->withHeader('Access-Control-Allow-Origin', '*' );
});
// เพิ่ม Admin/User Folder
$app->post('/folder/authority', function (Request $request, Response $response, $args) {
    $body = $request->getBody();
    $bodyArr = json_decode($body, true);
    $conn = $GLOBALS['dbconn'];
    $stmt = $conn->prepare( "INSERT into manage (CONTOLLER_USER_ID,MANAGR_FOLDER_ID,userid_manage) VALUES(?,?,?)" );
    $stmt->bind_param("iii",$bodyArr['CONTOLLER_USER_ID'],$bodyArr['MANAGR_FOLDER_ID'],$bodyArr['userid_manage']);
    $stmt->execute();
    $result = $stmt->affected_rows;
    $response->getBody()->write($result."");
    return $response->withHeader('Content-Type','application/json');
});

function delete_directory($dirname) {
    if (is_dir($dirname))
      $dir_handle = opendir($dirname);
        if (!$dir_handle)
            return false;
        while($file = readdir($dir_handle)) {
            if ($file != "." && $file != "..") {
                if (!is_dir($dirname."/".$file))
                        unlink($dirname."/".$file);
                else
                        delete_directory($dirname.'/'.$file);
            }
        }
        closedir($dir_handle);
        rmdir($dirname);
        return true;
}
// ลบ Folder
$app->post('/folder/delete', function (Request $request, Response $response, $args) {
    $body = $request->getBody();
    $bodyArr = json_decode($body, true);
    $conn = $GLOBALS['dbconn'];
    delete_directory( __DIR__."/../Files/".$bodyArr['folder_id'] );
    $stmt = $conn->prepare("DELETE FROM folder WHERE Folder_ID = ?");
    $stmt->bind_param("i",$bodyArr["folder_id"]);
    $stmt->execute();
    $result = $stmt->affected_rows;
    $response->getBody()->write($result."");
    return $response->withHeader('Content-Type','application/json')
                    ->withHeader('Access-Control-Allow-Origin', '*' );
});

// เปลี่ยนชื่อ Folder
$app->post('/folder/rename', function (Request $request, Response $response, $args) {
    $body = $request->getBody();
    $bodyArr = json_decode($body, true);
    $conn = $GLOBALS['dbconn'];
    $stmt = $conn->prepare( "UPDATE folder SET Fname = ? WHERE Folder_id = ?" );
    $stmt->bind_param("si",$bodyArr['Fname'],$bodyArr['Folder_id']);
    $stmt->execute();
    $result = $stmt->affected_rows;
    $response->getBody()->write($result."");
    return $response->withHeader('Content-Type','application/json')
                    ->withHeader('Access-Control-Allow-Origin', '*' );
});

// select Folder ของบุคคนนั้น ๆ 
$app->post('/folder', function (Request $request, Response $response, $args) {
    $body = $request->getBody();
    $bodyArr = json_decode($body, true);
    $conn = $GLOBALS['dbconn'];
    $stmt = $conn->prepare( "SELECT * FROM folder WHERE FK_Userid = ?" );
    $stmt->bind_param("i",$bodyArr['FK_Userid']);
    $stmt->execute();
    $result = $stmt->get_result();
    $FolderData = array();
        while($row = $result->fetch_assoc()) 
            array_push($FolderData, $row);
    $json = json_encode($FolderData);
    $response->getBody()->write($json);
    return $response->withHeader('Content-Type','application/json')
                    ->withHeader('Access-Control-Allow-Origin', '*' );
});



?>

