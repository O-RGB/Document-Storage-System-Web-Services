
<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

 
//  3. สมัครสมาชิก
//  3.1 เปลี่ยนแปลงข้อมูลส่วนตัว
//  3.2 เรียกช้อมูลทั้งหมดเฉพาะบุคคน
//  3.3 เข้าสู่ระบบ

// สมัครสมาชิก
$app->post('/Register', function (Request $request, Response $response, $args) {
    $body = $request->getBody();
    $bodyArr = json_decode($body, true);
    $bodyArr['password'] = password_hash($bodyArr['password'],PASSWORD_DEFAULT);
    $conn = $GLOBALS['dbconn'];
    $stmt = $conn->prepare("INSERT into user (password,username,Uname,level) values(?,?,?,?)");
    $stmt->bind_param("ssss",$bodyArr['password'],$bodyArr['username'],$bodyArr['Uname'],$bodyArr['level']);
    $stmt->execute();
    $result = $stmt->affected_rows;
    $response->getBody()->write($result."");
    return $response->withHeader('Content-Type','application/json')
                    ->withHeader('Access-Control-Allow-Origin', '*' );
});

// เปลี่ยนแปลงข้อมูลส่วนตัว
$app->post('/Editinfo', function (Request $request, Response $response, $args) {
    $body = $request->getBody();
    $bodyArr = json_decode($body, true);
    $bodyArr['password'] = password_hash($bodyArr['password'],PASSWORD_DEFAULT);
    $conn = $GLOBALS['dbconn'];
    $stmt = $conn->prepare("UPDATE user SET password = ? ,username = ?,Uname = ?,level = ? WHERE User_ID = ?");
    $stmt->bind_param("ssssi",$bodyArr['password'],$bodyArr['username'],$bodyArr['Uname'],$bodyArr['level'],$bodyArr['User_ID']);
    $stmt->execute();
    $result = $stmt->affected_rows;
    $response->getBody()->write($result."");
    return $response->withHeader('Content-Type','application/json')
                    ->withHeader('Access-Control-Allow-Origin', '*' );
});

// เรียกช้อมูลทั้งหมดเฉพาะบุคคน
$app->get('/userinfo/{id}', function (Request $request, Response $response, $args) {
    $user_id = $args['id'];
    $conn = $GLOBALS['dbconn'];
    $stmt = $conn->prepare("SELECT Uname,level from user WHERE User_ID = ?");
    $stmt->bind_param("s",$user_id);
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

// เข้าสู่ระบบ
$app->post('/login', function (Request $request, Response $response, $args) {
    $body = $request->getBody();
    $bodyArr = json_decode($body, true);
    $conn = $GLOBALS['dbconn'];
    $stmt = $conn->prepare("SELECT * from user WHERE username = ?");
    $stmt->bind_param("s",$bodyArr['username']);
    $stmt->execute();
    $result = $stmt->get_result();
    $password_DB = $result->fetch_assoc();
    $verify = password_verify($bodyArr['password'],$password_DB['password']);
    $userdata = array('User_ID' => $password_DB['User_ID'],
                      'Uname' => $password_DB['Uname'],
                      'level' => $password_DB['level'],
                      'username' => $bodyArr['username'],
                      'password' => $password_DB['password'],
                      'verify' => $verify);
    $json = json_encode($userdata);
    $response->getBody()->write($json);
    return $response->withHeader('Content-Type','application/json')
                    ->withHeader('Access-Control-Allow-Origin', '*' );
                    
});



?>