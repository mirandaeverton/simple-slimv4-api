<?php

class UserController
{
    private $request;
    private $response;
    private $args;
    private $user;

    public function __construct($request = null, $response = null, $args = null)
    {
        include_once  __DIR__ . '\..\..\models\User.php';

        $this->request = $request;
        $this->response = $response;
        $this->args = $args;
        $this->user = new User();
    }

    public function getAll()
    {
        $result = $this->user->read();

        $num = $result->rowCount();

        if ($num > 0) {
            $users_arr = array();
            $users_arr['data'] = array();

            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                extract($row);

                $user_item = array(
                    'id' => $id,
                    'name' => $name,
                    'email' => $email,
                    'isAdmin' => $isAdmin,
                );
                array_push($users_arr['data'], $user_item);
            }
            $data = json_encode($users_arr);
            $status = 200;
        } else {
            $data = json_encode(array('message' => 'No Users Found'));
            $status = 404;
        }
        
        return $this->makeResponse($data, $status);
    }

    public function getSingle()
    {
        $this->user->id = isset($this->args['id']) ? $this->args['id'] : die();

        $result = $this->user->read_single();

        $num = $result->rowCount();

        if ($num > 0) {
            $users_arr = array();
            $users_arr['data'] = array();

            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                extract($row);

                $user_item = array(
                    'id' => $id,
                    'name' => $name,
                    'email' => $email,
                    'isAdmin' => $isAdmin,
                );
                array_push($users_arr['data'], $user_item);
            }
            $data = json_encode($users_arr);
            $status = 200;
        } else {
            $data = json_encode(array('message' => "User With id {$this->user->id} Not Found"));
            $status = 404;
        }
        return $this->makeResponse($data, $status);
    }

    /*
     * @Param $userName string
     * @param $userPassword 
    */
    public function getSingleByNameAndPassword($userName, $userPassword) {
        
        $result = $this->user->read_single_by_name_and_password($userName, $userPassword);

        $num = $result->rowCount();

        if ($num > 0) {
            $user_arr = array();

            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                extract($row);

                $user_item = array(
                    'id' => $id,
                    'name' => $name,
                    'email' => $email,
                    'isAdmin' => $isAdmin,
                );
            }
            $user_arr['data'] = $user_item;
            $user_arr['status'] = 200;
        } else {
            $user_arr['data'] = array('message' => "Invalid name and password combination!");
            $user_arr['status'] = 400;
        }

        return $user_arr;
    }

    public function create()
    {
        $data = $this->request->getParsedBody();
        
        $this->user->name = $data['name'];
        $this->user->password = $data['password'];
        $this->user->email = $data['email'];
        $this->user->isAdmin = $data['isAdmin'];

        if ($this->user->create()) {
            $data = json_encode(
                array('message' => 'User Created')
            );
            $status = 201;
        } else {
            $data = json_encode(
                array('message' => 'User Not Created')
            );
            $status = 400;
        }
        return $this->makeResponse($data, $status);
    }

    public function update()
    {
        $responseData = $this->request->getParsedBody();
        $this->user->id = isset($this->args['id']) ? $this->args['id'] : die();
        $this->user->name = $responseData['name'];
        $this->user->password = $responseData['password'];
        $this->user->email = $responseData['email'];
        $this->user->isAdmin = $responseData['isAdmin'];

        if ($this->user->update()) {
            $data = json_encode(
                array('message' => "User With id {$this->user->id} Updated")
            );
            $status = 200;
        } else {
            $data = json_encode(
                array('message' => "User With id {$this->user->id} Not Updated")
            );
            $status = 400;
        }
        return $this->makeResponse($data, $status);
    }

    public function delete()
    {
        $this->user->id = isset($this->args['id']) ? $this->args['id'] : die();

        if ($this->user->delete()) {
            $data = json_encode(
                array('message' => "User With id {$this->user->id} Deleted")
            );
            $status = 200;   
        } else {
            $data = json_encode(
                array('message' => "User With id {$this->user->id} Not Deleted")
            );
            $status = 400;
        }
        return $this->makeResponse($data, $status);
    }

    private function makeResponse($data, $status) {
        $this->response->getBody()->write($data);
        return $this->response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus($status);
    }
}
