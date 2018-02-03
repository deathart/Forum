<?php

namespace App\Controller\Forum;

use App\Entity\Category;
use App\Entity\Forum;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class ForumController.
 */
class ForumController extends BaseController
{
    /**
     * ForumController constructor.
     *
     * @param \Symfony\Component\HttpFoundation\Session\SessionInterface $session
     * @param \Symfony\Component\HttpFoundation\RequestStack             $request
     */
    public function __construct(SessionInterface $session, RequestStack $request)
    {
        parent::__construct($session, $request);
        $this->title = 'Forum';
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function index(): RedirectResponse
    {
        return $this->redirectToRoute('base');
    }

    /**
     * @param string $slug
     */
    public function show(string $slug)
    {
        $getInfoForum = $this->getDoctrine()->getRepository(Forum::class)->findOneBy(['slug' => $slug]);

        $getForumChild = $this->getDoctrine()->getRepository(Forum::class)->findByParent($getInfoForum->getId());

        $getInfoCat = $this->getDoctrine()->getRepository(Category::class)->findOneBy(['id' => $getInfoForum->getCategory()]);

        $this->breadcrumb[] = ['url' => 'category/'.$getInfoCat->getSlug(), 'name' => $getInfoCat->getName()];

        if (0 != $getInfoForum->getParent()) {
            $getForumParent = $this->getDoctrine()->getRepository(Forum::class)->findOneBy(['id' => $getInfoForum->getParent()]);
            $this->breadcrumb[] = ['url' => 'forum/'.$getForumParent->getSlug(), 'name' => $getForumParent->getName()];
        }

        $this->breadcrumb[] = ['url' => 'active', 'name' => $getInfoForum->getName()];

        $this->data['slug_forum'] = $slug;

        $this->data['cat_info'] = [
            'id' => $getInfoForum->getId(),
            'name' => $getInfoForum->getName(),
            'desc' => $getInfoForum->getDesc(),
            'slug' => $getInfoForum->getSlug(),
            'position' => $getInfoForum->getPosition(),
        ];

        $this->data['forum_child'] = $getForumChild;

        $this->stitle = $slug;

        return $this->renderer('forum/show.html.twig');
    }
}
