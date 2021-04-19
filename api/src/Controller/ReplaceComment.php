<?php
namespace App\Controller;

use App\Entity\Comment;
use App\Service\CommentPublishingHandler;

class ReplaceComment
{
    private $bookPublishingHandler;

    public function __construct(CommentPublishingHandler $bookPublishingHandler)
    {
        $this->bookPublishingHandler = $bookPublishingHandler;
    }

    public function __invoke(Comment $data): Comment
    {
        $this->bookPublishingHandler->handle($data);

        return $data;
    }

}
