<?php

class PostController
{
    private $request;
    private $response;
    private $args;
    private $post;

    public function __construct($request, $response, $args = null)
    {
        include_once  __DIR__ . '\..\..\models\Post.php';

        $this->request = $request;
        $this->response = $response;
        $this->args = $args;
        $this->post = new Post();
    }

    public function getAll()
    {
        $result = $this->post->read();

        $num = $result->rowCount();

        if ($num > 0) {
            $posts_arr = array();
            $posts_arr['data'] = array();

            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                extract($row);

                $post_item = array(
                    'id' => $id,
                    'title' => $title,
                    'body' => html_entity_decode($body),
                    'author' => $author,
                    'category_id' => $category_id,
                    'category_name' => $category_name,
                );
                array_push($posts_arr['data'], $post_item);
            }
            $data = json_encode($posts_arr);
            $status = 200;
        } else {
            $data = json_encode(array('message' => 'No Posts Found'));
            $status = 404;
        }
        return $this->makeResponse($data, $status);
    }

    public function getSingle()
    {
        $this->post->id = isset($this->args['id']) ? $this->args['id'] : die();

        $result = $this->post->read_single();

        $num = $result->rowCount();

        if ($num > 0) {
            $posts_arr = array();
            $posts_arr['data'] = array();

            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                extract($row);

                $post_item = array(
                    'id' => $id,
                    'title' => $title,
                    'body' => html_entity_decode($body),
                    'author' => $author,
                    'category_id' => $category_id,
                    'category_name' => $category_name,
                );
                array_push($posts_arr['data'], $post_item);
            }
            $data = json_encode($posts_arr);
            $status = 200;
        } else {
            $data = json_encode(array('message' => "Post With id {$this->post->id} Not Found"));
            $status = 404;
        }
        return $this->makeResponse($data, $status);
    }

    public function create()
    {
        $data = $this->request->getParsedBody();
        
        $this->post->title = $data['title'];
        $this->post->body = $data['body'];
        $this->post->author = $data['author'];
        $this->post->category_id = $data['category_id'];

        if ($this->post->create()) {
            $data = json_encode(
                array('message' => 'Post Created')
            );
            $status = 201;
        } else {
            $data = json_encode(
                array('message' => 'Post Not Created')
            );
            $status = 400;
        }
        return $this->makeResponse($data, $status);
    }

    public function update()
    {
        $responseData = $this->request->getParsedBody();
        $this->post->id = isset($this->args['id']) ? $this->args['id'] : die();
        $this->post->title = $responseData['title'];
        $this->post->body = $responseData['body'];
        $this->post->author = $responseData['author'];
        $this->post->category_id = $responseData['category_id'];

        if ($this->post->update()) {
            $data = json_encode(
                array('message' => "Post With id {$this->post->id} Updated")
            );
            $status = 200;
        } else {
            $data = json_encode(
                array('message' => "Post With id {$this->post->id} Not Updated")
            );
            $status = 400;
        }
        return $this->makeResponse($data, $status);
    }

    public function delete()
    {
        $this->post->id = isset($this->args['id']) ? $this->args['id'] : die();

        if ($this->post->delete()) {
            $data = json_encode(
                array('message' => "Post With id {$this->post->id} Deleted")
            );
            $status = 200;   
        } else {
            $data = json_encode(
                array('message' => "Post With id {$this->post->id} Not Deleted")
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
