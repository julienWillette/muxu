<?php
/**
 * Created by PhpStorm.
 * User: sylvain
 * Date: 07/03/18
 * Time: 18:20
 * PHP version 7
 */

namespace App\Model;

/**
 *
 */
class ArtistManager extends AbstractManager
{
    /**
     *
     */
    const TABLE = 'artist';

    /**
     *  Initializes this class.
     */
    public function __construct()
    {
        parent::__construct(self::TABLE);
    }


    /**
     * @param array $artist
     * @return int
     */
    public function insert(array $artist): int
    {
        // prepared request
        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE . " (`firstname`, `lastname`, `description`) 
        VALUES (:firstname, :lastname, :description)");
        $statement->bindValue('firstname', $artist['firstname'], \PDO::PARAM_STR);
        $statement->bindValue('lastname', $artist['lastname'], \PDO::PARAM_STR);
        $statement->bindValue('description', $artist['description'], \PDO::PARAM_STR);

        if ($statement->execute()) {
            return (int)$this->pdo->lastInsertId();
        }
    }


    /**
     * @param array $artist
     * @return bool
     */
    public function update(array $artist):bool
    {
        $statement = $this->pdo->prepare("UPDATE " . self::TABLE . "
         SET `firstname` = :firstname, `lastname` = :lastname, `description` = :description WHERE id=:id");
        $statement->bindValue('id', $artist['id'], \PDO::PARAM_INT);
        $statement->bindValue('firstname', $artist['firstname'], \PDO::PARAM_STR);
        $statement->bindValue('lastname', $artist['lastname'], \PDO::PARAM_STR);
        $statement->bindValue('description', $artist['description'], \PDO::PARAM_STR);

        return $statement->execute();
    }

    public function selectAll(): array
    {
        $artists = $this->pdo->query("SELECT 
            artist.id,
            artist.firstname,
            artist.lastname,
            artist.description   
            FROM artist ORDER BY lastname ASC")->fetchAll();
        
        $result = [];
        foreach ($artists as $artist) {
            $statementImg = $this->pdo->prepare('SELECT url FROM img WHERE artist_id=:artist_id');
            $statementImg->bindValue('artist_id', $artist['id'], \PDO::PARAM_INT);
            $statementImg->execute();
            $images = $statementImg->fetchAll();
            $artist['images'] = $images;
            array_push($result, $artist);
        }

        return $result;
    }

    public function selectOneById(int $id)
    {
        $statement = $this->pdo->prepare("SELECT 
            artist.id,
            artist.firstname,
            artist.lastname,
            artist.description,
            img.url as url    
            FROM artist 
            LEFT JOIN img ON img.artist_id=artist.id
            WHERE artist.id=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetch();
    }

    public function selectAllImgByArtist($id)
    {
        $statement = $this->pdo->prepare("SELECT 
            img.url 
            FROM img 
            WHERE img.artist_id=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }
}
