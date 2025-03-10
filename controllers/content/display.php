<?php
session_start();
include('connector.php');
include('library.php');
$json = file_get_contents('php://input');
$data = json_decode($json, true);
$name = $_SESSION['user']; 
if(isset($data['type']) && $data['type'] === 'posts')
{

    if(isset($data['parent'])){
        $parent = $data['parent'];
        if(isset($data['user']))
        {
            $user = $data['user'];
            $sql = "SELECT paths.*, profile.photo FROM paths INNER JOIN profile ON profile.name = paths.name WHERE paths.parent='$parent' AND paths.name='$user' AND (paths.access = 'public' 
                         OR (paths.access = 'friends' AND paths.name IN 
                            (SELECT CASE 
                                    WHEN user_2 = '$name' THEN user_1 
                                    ELSE user_2 
                                    END AS friend 
                             FROM friends 
                             WHERE user_1 = '$name' 
                             OR user_2 = '$name'))) OR ('$user' = '$name' AND paths.name='$name')  ORDER BY date DESC";
        }
        else if(isset($data['club']))
        {
            $club = $data['club'];
            $sql = "SELECT paths.*, profile.photo FROM paths INNER JOIN profile ON profile.name = paths.name WHERE paths.parent='$parent' AND paths.access='public' AND profile.name IN(SELECT user FROM club_user WHERE club='$club') ORDER BY date DESC";
        }
        else 
        {
            $sql = "SELECT paths.*, profile.photo FROM paths INNER JOIN profile ON profile.name = paths.name WHERE paths.parent='$parent' AND (paths.access = 'public' 
                         OR (paths.access = 'friends' AND paths.name IN 
                            (SELECT CASE 
                                    WHEN user_2 = '$name' THEN user_1 
                                    ELSE user_2 
                                    END AS friend 
                             FROM friends 
                             WHERE user_1 = '$name' 
                             OR user_2 = '$name'))) ORDER BY date DESC";
        }
    }
    else {
        $sql = "SELECT paths.*, profile.photo FROM paths INNER JOIN profile ON profile.name = paths.name WHERE paths.name IN (SELECT CASE 
        WHEN user_2 = '$name' THEN user_1 
        ELSE user_2 
        END AS friend FROM friends WHERE user_1='$name' OR user_2='$name') ORDER BY date DESC";
    }
}

else if($data['type'] === 'clubs')
{
    $sql = "SELECT * FROM clubs ORDER BY point DESC";
}

else if($data['type'] === 'pins')
{
    if(isset($data['club'])) {
        $club = $data['club'];
        $sql = "SELECT paths.conf, paths.body, paths.title, paths.access, paths.name, pins.quote, paths.url, profile.photo as pinnerPhoto, profile.name as pinnerName FROM pins INNER JOIN paths ON paths.url = pins.url INNER JOIN profile ON profile.name = pins.name WHERE pins.club='$club' ORDER BY pins.date DESC";
    }
}

else if($data['type'] === 'friends')
{
    if(isset($data['user'])) {
        $user = $data['user'];
        $sql = "SELECT * FROM profile WHERE name IN (SELECT CASE 
        WHEN user_2 = '$user' THEN user_1 
        ELSE user_2 
        END AS friend FROM friends WHERE user_1='$user' OR user_2='$user')";
    }
}
else if($data['type'] === 'gossip')
{
    if(isset($data['club'])) {
        $club = $data['club'];
        $sql = "SELECT * FROM gossip WHERE club='$club' AND date >= NOW() - INTERVAL 1 DAY";
    }
}
else if($data['type'] === 'gallery')
{
    if(isset($data['club'])) {
        $club = $data['club'];
        $sql = "SELECT conf, url FROM paths WHERE conf!='' AND access='public' AND name IN(SELECT user FROM club_user WHERE club='$club')";
    }
    else if(isset($data['user'])) {
        $user = $data['user'];
        $sql = "SELECT conf, url FROM paths WHERE conf!='' AND access='public' AND name='$user'";
    }
}
else if($data['type'] === 'diary')
{

        $sql = "SELECT 
    profile.*,
    diary.message,
    diary.date
FROM profile
INNER JOIN diary ON profile.name = diary.name
WHERE (
    profile.name = '$name'
    OR profile.name IN (
        SELECT 
            CASE
                WHEN friends.user_2 = '$name' THEN friends.user_1
                ELSE friends.user_2
            END
        FROM friends
        WHERE friends.user_1 = '$name' 
        OR friends.user_2 = '$name'
    )
)
AND diary.date >= NOW() - INTERVAL 1 DAY
ORDER BY diary.date DESC;";
    
}




$result = mysqli_query($conn, $sql);
$data = array();

while($row = mysqli_fetch_array($result)) {
    $data[] = $row;
}


echo (json_encode($data));
?>
