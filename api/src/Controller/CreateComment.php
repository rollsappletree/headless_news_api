<?php
namespace App\Controller;

use App\Entity\Comment;
use App\Repository\NewsRepository;
use App\Service\CommentPublishingHandler;

class CreateComment
{
    private CommentPublishingHandler $commentHandler;
    private NewsRepository $newsRepository;

    public function __construct(CommentPublishingHandler $commentHandler, NewsRepository $newsRepository)
    {
        $this->commentHandler = $commentHandler;
        $this->newsRepository = $newsRepository;
    }

    public function __invoke(Comment $data, int $id): Comment
    {
        $news = $this->newsRepository->findOneBy(['id' =>    $id]);
        $data->setNews($news);
        $this->commentHandler->handle($data);

        return $data;
    }

}
