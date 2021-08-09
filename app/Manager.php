<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;




// select folder nmanager *MG
$app->get('/manager/{id}', function (Request $request, Response $response, $args) {
    $manager_id = $args['id'];
    $conn = $GLOBALS['dbconn'];
    $stmt = $conn->prepare("SELECT manage.CONTOLLER_USER_ID,folder.Folder_ID,folder.Fname
                            FROM manage,folder
                            where folder.Folder_ID = MANAGR_FOLDER_ID
                            and manage_level = 'MG'
                            and userid_manage = ?
                           ");
    $stmt->bind_param("i",$manager_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $ManagerData = array();
        while($row = $result->fetch_assoc()) 
            array_push($ManagerData, $row);
    $json = json_encode($ManagerData);
    $response->getBody()->write($json);
    return $response->withHeader('Content-Type','application/json')
                    ->withHeader('Access-Control-Allow-Origin', '*' );
});

// select fileall user manager *MG
$app->get('/manager/file/{id}', function (Request $request, Response $response, $args) {
    $manager_id = $args['id'];
    $conn = $GLOBALS['dbconn'];
    $stmt = $conn->prepare("SELECT pdf.PDF_ID ,
                            pdf.PDF_name ,
                            pdf.FK_USERID ,
                            pdf.Doc_number,
                            pdf.Doc_source ,
                            pdf.Doc_data ,
                            pdf.referenced ,
                            pdf.sent_to ,
                            pdf.Title ,
                            pdf.FK_FOLDERID
                            from folder,manage,pdf
                            WHERE folder.Folder_ID = manage.MANAGR_FOLDER_ID
                            and pdf.FK_FOLDERID = manage.MANAGR_FOLDER_ID
                            and manage.manage_level = 'MG'
                            and manage.userid_manage = ?
                           ");
    $stmt->bind_param("i",$manager_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $ManagerData = array();
        while($row = $result->fetch_assoc()) 
            array_push($ManagerData, $row);
    $json = json_encode($ManagerData);
    $response->getBody()->write($json);
    return $response->withHeader('Content-Type','application/json')
                    ->withHeader('Access-Control-Allow-Origin', '*' );
});


// select folder nmanager *UR
$app->get('/manager/UR/{id}', function (Request $request, Response $response, $args) {
    $manager_id = $args['id'];
    $conn = $GLOBALS['dbconn'];
    $stmt = $conn->prepare("SELECT manage.CONTOLLER_USER_ID,folder.Folder_ID,folder.Fname
                            FROM manage,folder
                            where folder.Folder_ID = MANAGR_FOLDER_ID
                            and manage_level = 'UR'
                            and userid_manage = ?
                           ");
    $stmt->bind_param("i",$manager_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $ManagerData = array();
        while($row = $result->fetch_assoc()) 
            array_push($ManagerData, $row);
    $json = json_encode($ManagerData);
    $response->getBody()->write($json);
    return $response->withHeader('Content-Type','application/json')
                    ->withHeader('Access-Control-Allow-Origin', '*' );
});


// select fileall user manager *UR
$app->get('/manager/file/UR/{id}', function (Request $request, Response $response, $args) {
    $manager_id = $args['id'];
    $conn = $GLOBALS['dbconn'];
    $stmt = $conn->prepare("SELECT pdf.PDF_ID ,
                            pdf.PDF_name ,
                            pdf.FK_USERID ,
                            pdf.Doc_number,
                            pdf.Doc_source ,
                            pdf.Doc_data ,
                            pdf.referenced ,
                            pdf.sent_to ,
                            pdf.Title ,
                            pdf.FK_FOLDERID
                            from folder,manage,pdf
                            WHERE folder.Folder_ID = manage.MANAGR_FOLDER_ID
                            and pdf.FK_FOLDERID = manage.MANAGR_FOLDER_ID
                            and manage.manage_level = 'UR'
                            and manage.userid_manage = ?
                           ");
    $stmt->bind_param("i",$manager_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $ManagerData = array();
        while($row = $result->fetch_assoc()) 
            array_push($ManagerData, $row);
    $json = json_encode($ManagerData);
    $response->getBody()->write($json);
    return $response->withHeader('Content-Type','application/json')
                    ->withHeader('Access-Control-Allow-Origin', '*' );
});


// select manager user  *UR||MG
$app->get('/manager/folder/code/{id}', function (Request $request, Response $response, $args) {
    $manager_id = $args['id'];
    $conn = $GLOBALS['dbconn'];
    $stmt = $conn->prepare("SELECT  user.User_ID,
                                    user.Uname,
                                    manage.MANAGR_FOLDER_ID,
                                    manage.manage_level
                            FROM user,manage
                            WHERE manage.userid_manage = user.User_ID
                            and manage.MANAGR_FOLDER_ID = ?
                           ");
    $stmt->bind_param("i",$manager_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $ManagerData = array();
        while($row = $result->fetch_assoc()) 
            array_push($ManagerData, $row);
    $json = json_encode($ManagerData);
    $response->getBody()->write($json);
    return $response->withHeader('Content-Type','application/json')
                    ->withHeader('Access-Control-Allow-Origin', '*' );
});

// add user Manager *UR||MG
$app->post('/manager/add', function (Request $request, Response $response, $args) {
    $body = $request->getBody();
    $bodyArr = json_decode($body, true);
    $conn = $GLOBALS['dbconn'];
    $stmt = $conn->prepare("SELECT user.User_ID FROM user 
                            WHERE user.Uname = ?");
    $stmt->bind_param("s",$bodyArr['Uname']);
    $stmt->execute();
    $result = $stmt->get_result();
    $UserID = $result->fetch_assoc();
    $stmt = $conn->prepare( "INSERT into manage 
                                (CONTOLLER_USER_ID,MANAGR_FOLDER_ID,userid_manage,manage_level) 
                                values(?,?,?,?)" 
                           );
    $stmt->bind_param("iiis",$bodyArr['CONTOLLER_USER_ID'],
                            $bodyArr['MANAGR_FOLDER_ID'],
                            $UserID['User_ID'],
                            $bodyArr['manage_level']
                     );
    $stmt->execute();
    $result = $stmt->affected_rows;
    $response->getBody()->write($result."");
    return $response->withHeader('Content-Type','application/json')
                    ->withHeader('Access-Control-Allow-Origin', '*' );
});

// remove user Manager *UR||MG
$app->post('/manager/remove', function (Request $request, Response $response, $args) {
    $body = $request->getBody();
    $bodyArr = json_decode($body, true);
    $conn = $GLOBALS['dbconn'];

    $stmt = $conn->prepare( "DELETE FROM manage 
                            WHERE CONTOLLER_USER_ID = ? 
                            and MANAGR_FOLDER_ID = ? 
                            and userid_manage = ? " 
                           );
    $stmt->bind_param("iii",$bodyArr['CONTOLLER_USER_ID'],
                            $bodyArr['MANAGR_FOLDER_ID'],
                            $bodyArr['userid_manage']
                     );
    $stmt->execute();
    $result = $stmt->affected_rows;
    $response->getBody()->write($result."");
    return $response->withHeader('Content-Type','application/json')
                    ->withHeader('Access-Control-Allow-Origin', '*' );
});
?>