<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

// 2. ไฟล์
//  2.1 อัปโหลดไฟล์  
//  2.2 แก้ไข input 
//  2.3 ลบและอัปโหลดไฟล์ใน Folder 
//  2.4 ค้นหาไฟล์ 
//  2.5 input ข้อมูล

// อัปโหลดไฟล์ 
function bass64Tofile($Base64String,$outputFile){
    $file = fopen($outputFile,'wb');
    $data = explode(',',$Base64String);
    if(count($data) == 2)
    {
        fwrite($file,base64_decode($data[1]));
    }else{
        fwrite($file,base64_decode($data[0]));
    }
    
    fclose($file);
}
$app->post('/file/upload', function (Request $request, Response $response, array $args) {
    $json = $request->getBody();
    $jsonArray = json_decode($json,true);
        try{

            if(!file_exists(__DIR__."/../Files/".$jsonArray['Folder_ID'])){
                mkdir(__DIR__."/../Files/".$jsonArray['Folder_ID']);
            }            
            bass64Tofile($jsonArray['base64'], __DIR__ . '/../Files/'.$jsonArray['Folder_ID'] .'/'. $jsonArray['filename']);
            $response->getBody()->write(json_encode("file upload..."));
            return $response->withHeader('Content-Type','application/json')->withStatus(200)
            ->withHeader('Access-Control-Allow-Origin', '*' );      
        }

        catch(Exception $error){
            $response->getBody()->write(json_encode("file Error...".$error.""));
            return $response->withHeader('Content-Type','application/json')->withStatus(200) 
            ->withHeader('Access-Control-Allow-Origin', '*' );    
        }

});


//  input ข้อมูล
$app->post('/file/upload/deteil', function (Request $request, Response $response, $args) {
    $body = $request->getBody();
    $bodyArr = json_decode($body, true);
    $conn = $GLOBALS['dbconn'];
    $stmt = $conn->prepare("INSERT into pdf 
                            (PDF_name,FK_USERID,Doc_number,Doc_source,Doc_data,referenced,sent_to,Title,FK_FOLDERID) 
                            values(?,?,?,?,?,?,?,?,?)");
    $stmt->bind_param("sissssssi",
                            $bodyArr['PDF_name'],
                            $bodyArr['FK_USERID'],
                            $bodyArr['Doc_number'],
                            $bodyArr['Doc_source'],
                            $bodyArr['Doc_data'],
                            $bodyArr['referenced'],
                            $bodyArr['sent_to'],
                            $bodyArr['Title'],
                            $bodyArr['FK_FOLDERID']
                     );
    $stmt->execute();
    $result = $stmt->affected_rows;
    $response->getBody()->write($result."");
    return $response->withHeader('Content-Type','application/json')
    ->withHeader('Access-Control-Allow-Origin', '*' );
});

//  ลบและอัปโหลดไฟล์ใน Folder 
$app->post('/file/deteil', function (Request $request, Response $response, $args) {
    $body = $request->getBody();
    $bodyArr = json_decode($body, true);
    $conn = $GLOBALS['dbconn'];
    $stmt = $conn->prepare("UPDATE pdf SET PDF_name = ? where FK_FOLDERID = ?");
    $stmt->bind_param("si",
                            $bodyArr['PDF_name'],
                            $bodyArr['FK_FOLDERID']
                     );
    $stmt->execute();
    $result = $stmt->affected_rows;
    $response->getBody()->write($result."");
    return $response->withHeader('Content-Type','application/json');
});

//เรียกไฟล์ USER
$app->get('/file/{id}', function (Request $request, Response $response, $args) {
    $FK_USERID = $args['id'];
    $conn = $GLOBALS['dbconn'];
    $stmt = $conn->prepare("SELECT * from pdf WHERE FK_USERID = ?");
    $stmt->bind_param("s",$FK_USERID);
    $stmt->execute();
    $result = $stmt->get_result();
    $UserData = array();
        while($row = $result->fetch_assoc()) 
            array_push($UserData, $row);
    $json = json_encode($UserData);
    $response->getBody()->write($json);
    return $response->withHeader('Content-Type','application/json')
                    ->withHeader('Access-Control-Allow-Origin', '*' );
});

//ลบไฟล์ USER
$app->post('/file/delete', function (Request $request, Response $response, $args) {
    $body = $request->getBody();
    $bodyArr = json_decode($body, true);
    $conn = $GLOBALS['dbconn'];
    unlink( __DIR__."/../Files/".$bodyArr['FK_FOLDERID']."/".$bodyArr['PDF_name'] );
    $stmt = $conn->prepare("DELETE from pdf WHERE PDF_ID = ?");
    $stmt->bind_param("i",$bodyArr['PDF_ID']);
    $stmt->execute();
    $result = $stmt->affected_rows;
    $response->getBody()->write($result."");
    return $response->withHeader('Content-Type','application/json')
                    ->withHeader('Access-Control-Allow-Origin', '*' );
       
});






?>
