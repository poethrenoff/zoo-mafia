<?php
namespace Adminko\Model;

class ReviewModel extends Model
{
    // Возвращает пользователя
    public function getClient()
    {
        return Model::factory('client')->get($this->getReviewClient());
    }
}
