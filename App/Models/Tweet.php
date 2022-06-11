<?php
    namespace App\Models;
    use MF\Model\Model;

    class Tweet extends Model {
        private $id;
        private $id_usuario;
        private $tweet;
        private $data;

        public function __get($attr)
        {
            return $this->$attr;
        }

        public function __set($attr, $value)
        {
            $this->$attr = $value;
        }

        public function salvar() {
            $query = "
                INSERT INTO tweets(id_usuario, tweet) VALUES (:id_usuario , :tweet);
            ";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id_usuario' , $this->__get('id_usuario'));
            $stmt->bindValue(':tweet' , $this->__get('tweet'));
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }

        public function getAll() {
            $query = "
            SELECT 
                t.id, t.id_usuario, u.nome,  t.tweet, DATE_FORMAT(t.data, '%d/%m/%Y %H:%i') as data 
            FROM 
                tweets as t
                left join usuarios as u on (t.id_usuario = u.id)
            WHERE 
                t.id_usuario = :id_usuario or t.id_usuario in (SELECT id_usuario_seguindo FROM usuarios_seguidores WHERE id_usuario = :id_usuario)
            ORDER BY
                t.data desc
            ";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id_usuario' , $this->__get('id_usuario'));
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }

        public function getPorPagina($limit, $offset) {
            $query = "
            SELECT 
                t.id, t.id_usuario, u.nome, u.foto_perfil,  t.tweet, DATE_FORMAT(t.data, '%d/%m/%Y %H:%i') as data 
            FROM 
                tweets as t
                left join usuarios as u on (t.id_usuario = u.id)
            WHERE 
                t.id_usuario = :id_usuario or t.id_usuario in (SELECT id_usuario_seguindo FROM usuarios_seguidores WHERE id_usuario = :id_usuario)
            ORDER BY
                t.data desc
            LIMIT
                $limit
            OFFSET
                $offset
            ";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id_usuario' , $this->__get('id_usuario'));
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }

        public function totalTweets()
        {
            $query = "
            SELECT 
                count(*) as total
            FROM 
                tweets as t
                left join usuarios as u on (t.id_usuario = u.id)
            WHERE 
                t.id_usuario = :id_usuario 
                or t.id_usuario in (SELECT id_usuario_seguindo FROM usuarios_seguidores WHERE id_usuario = :id_usuario)
            ";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id_usuario' , $this->__get('id_usuario'));
            $stmt->execute();

            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }

        public function excluir()
        {
           $query = "
                DELETE FROM tweets WHERE id = :id_tweet
           ";
           $stmt = $this->db->prepare($query);
           $stmt->bindValue(':id_tweet' , $this->__get('id'));
           $stmt->execute();

           return true;
        }
    }
?>