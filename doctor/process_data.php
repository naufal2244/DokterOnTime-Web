<?php

//process_data.php

header('Content-Type: application/json');

try {
    $connect = new PDO("mysql:host=localhost;dbname=clinic_appointment", "root", "");
    $connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if(isset($_POST["query"])) {
        $data = array();
        $condition = preg_replace('/[^A-Za-z0-9\- ]/', '', $_POST["query"]);

        $query = "
            SELECT nama_obat FROM obat
            WHERE nama_obat LIKE :condition
            ORDER BY id_obat
            LIMIT 10
        ";

        $statement = $connect->prepare($query);
        $statement->execute([':condition' => '%' . $condition . '%']);

        $replace_string = '<b>'.$condition.'</b>';

        foreach($statement as $row) {
            $data[] = array(
                'nama_obat' => str_ireplace($condition, $replace_string, $row["nama_obat"])
            );
        }

        echo json_encode($data);
        exit;
    }

    $post_data = json_decode(file_get_contents('php://input'), true);

    if(isset($post_data['search_query'])) {
        $data = array(
            ':search_query' => $post_data['search_query']
        );

        $query = "
            SELECT search_id FROM recent_search 
            WHERE search_query = :search_query
        ";

        $statement = $connect->prepare($query);
        $statement->execute($data);

        if($statement->rowCount() == 0) {
            $query = "
            INSERT INTO recent_search 
            (search_query) VALUES (:search_query)
            ";

            $statement = $connect->prepare($query);
            $statement->execute($data);
        }

        $output = array(
            'success' => true
        );

        echo json_encode($output);
        exit;
    }

    if(isset($post_data['action']) && $post_data['action'] == 'fetch') {
        $query = "SELECT * FROM recent_search ORDER BY search_id DESC LIMIT 10";

        $result = $connect->query($query);

        $data = array();

        foreach($result as $row) {
            $data[] = array(
                'id' => $row['search_id'],
                'search_query' => $row["search_query"]
            );
        }

        echo json_encode($data);
        exit;
    }
} catch (Exception $e) {
    echo json_encode(array('error' => $e->getMessage()));
    exit;
}