<?php
namespace App\Controller;

use App\Entity\Comment;
use App\Service\CommentPublishingHandler;

class RemoveComment
{
    private CommentPublishingHandler $commentHandler;

    public function __construct(CommentPublishingHandler $commentHandler)
    {
        $this->commentHandler = $commentHandler;
    }

    public function __invoke(Comment $data): Comment
    {
        $this->commentHandler->handle($data);

        return $data;
    }

}
