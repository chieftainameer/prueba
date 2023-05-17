<?php

class RandomUserAPI
{
    private $url = 'https://randomuser.me/api?results=10';
    private $dbHost = 'localhost';
    private $dbName = 'prueba';
    private $dbUser = 'root';
    private $dbPass = '';

    public function getUsers()
    {
        // Initialize cURL session
        try {
            $curl = curl_init();

            $response = $this->curlConnection($curl,$this->url);
            // Close the cURL session
            curl_close($curl);

            // Process the response
            if ($response) {
                // Decode the JSON response
                $users = json_decode($response, true);
                $users = $users['results'];
                // Save users to the database
                $pdo = new PDO("mysql:host=$this->dbHost;dbname=$this->dbName", $this->dbUser, $this->dbPass);
                foreach ($users as $user) {
                    $name = $user['name']['first'] . ' ' . $user['name']['last'];
                    $email = $user['email'];
                    $picture = $user['picture']['thumbnail'];

                    // Insert user data into the database table
                    $stmt = $pdo->prepare("INSERT INTO users (name, email, image) VALUES (?, ?, ?)");
                    $stmt->execute([$name, $email, $picture]);
                }

            }
        }
        catch(Exception $e)
        {
            return ['message' => 'error','code' => 404,'data' => []];
        }
        return ['message' => 'success','code' => 200,'data' => $users];

    }

    public function curlConnection($curl,$url){
        // Set cURL options
        curl_setopt($curl, CURLOPT_URL, $url);  // Set the URL
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  // Return the response as a string
        curl_setopt($curl, CURLOPT_HEADER, false);  // Exclude the header from the response

        // Disable SSL certificate verification
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        // Execute the cURL request
        $response = curl_exec($curl);

        // Check for cURL errors
        if (curl_errno($curl)) {
            throw new Exception(curl_error($curl));
        }
        return $response;
    }
}

// Create an instance of the RandomUserAPI class
$randomUserAPI = new RandomUserAPI();

// Get the user data and echo it as JSON
$users = $randomUserAPI->getUsers();
echo json_encode($users);
