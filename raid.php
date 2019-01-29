<?php
$dbhost = "hostname/ip";
$dbuser = "rdmuser";
$dbpass = "password";
$dbname = "rdmdb";
$dbtimezone = "UTC";
$displaytimezone = "UTC";

function getFormattedTimeFromTimestamp( $columnName, $dbtimezone, $displaytimezone ) {
	return "time_format(convert_tz(from_unixtime($columnName), '$dbtimezone', '$displaytimezone'), '%h:%i:%s %p')";
}

// Establish connection to database
try{
    $pdo = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e){
    die("ERROR: Could not connect. " . $e->getMessage());
}
// Query Database and Build Raid Billboard
try 
{
    $sql = "
SELECT
    " . getFormattedTimeFromTimestamp( 'raid_battle_timestamp', $dbtimezone, $displaytimezone ) . ",
    " . getFormattedTimeFromTimestamp( 'raid_end_timestamp', $dbtimezone, $displaytimezone ) . ",
    raid_level,
    pokedex.name,
    gym.name
FROM gym
    INNER JOIN pokedex
        ON gym.raid_pokemon_id = pokedex.pokemon_id
WHERE raid_pokemon_id IS NOT NULL
    && gym.name IS NOT NULL
ORDER BY raid_end_timestamp
";
        $result = $pdo->query($sql);
        if($result->rowCount() > 0){
            echo "<table border='1';>";
                echo "<tr>";
                    echo "<th>Raid Starts</th>";
                    echo "<th>Raid Ends</th>";
                    echo "<th>Raid Level</th>";
                    echo "<th>Raid Boss</th>";
                    echo "<th>Gym</th>";
                echo "</tr>";
            while($row = $result->fetch()){
                echo "<tr>";
                    echo "<td>" . $row[0] . "</td>";
                    echo "<td>" . $row[1] . "</td>";
                    echo "<td>" . $row['raid_level'] . "</td>";
                    echo "<td>" . $row[3] . "</td>";
                    echo "<td>" . $row['name'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
// Free result set
        unset($result);
    } else{
        echo "No records matching your query were found.";
    }
} catch(PDOException $e){
    die("ERROR: Could not able to execute $sql. " . $e->getMessage());
}
// Close connection
unset($pdo);
?>
