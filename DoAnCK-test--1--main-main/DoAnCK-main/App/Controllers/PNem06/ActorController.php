<?php
require_once __DIR__ . '/../../Models/PNem06/Actor.php';

class ActorController {
    private $actorModel;

    public function __construct() {
        $this->actorModel = new Actor();
    }

    public function index($page = 1) {
    $limit = 6;
    $offset = ($page - 1) * $limit;

    $actors = $this->actorModel->getActorsPaginated($offset, $limit);
    $totalActors = $this->actorModel->getTotalActors();
    $totalPages = ceil($totalActors / $limit);

    // ✅ QUAN TRỌNG NHẤT
    foreach ($actors as $key => $actor) {
        $actors[$key]->movie_count = $this->actorModel->getMovieCount($actor->Actor_ID);
    }

    $currentPage = $page;
    $pageTitle = 'Danh sách diễn viên';

    include __DIR__ . '/../../Views/member/actor/list.php';
}

    public function showProfile($actor_id) {
    
    // ✅ LẤY DATA TỪ MODEL
    $actor = $this->actorModel->getActorById($actor_id);

    // ✅ CHECK
    if (!$actor) {
        $_SESSION['error'] = 'Diễn viên không tồn tại!';
        header('Location: index.php?controller=actor');
        exit;
    }

    // ✅ LẤY DANH SÁCH PHIM
    $movies = $this->actorModel->getMoviesByActor($actor_id);

    $pageTitle = $actor->Actor_Name;

    include __DIR__ . '/../../Views/member/actor/profile.php';
}
}
?>