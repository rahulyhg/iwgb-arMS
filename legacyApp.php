<?php

/** @noinspection SqlNoDataSourceInspection */
/** @noinspection SqlResolve */
/** @noinspection PhpUndefinedMethodInspection */

use \Slim\Http\Request as Request;
use \Slim\Http\Response as Response;

// setup Slim

//// $config['displayErrorDetails'] = true;
//$config['addContentLengthHeader'] = false;
//$config['db'] = array(
//    'host'  => 'localhost',
//    'user'  => $dbuser,
//    'pass'  => $dbpass,
//    'dbname'=> 'iwgb-cms',
//);
//$config['twilio'] = array(
//    'sid'   => $twilioSid,
//    'token' => $twilioToken,
//);
//$config['mailgun']['key'] = $mailgunKey;
//$config['iwgb']['joindata'] = $iwgbJoindata;
//$config['iwgb']['email'] = $iwgbContact;
//$config['recaptcha']['secret'] = $recaptchaSecret;
//$config['iwgb']['languages'] = array('en', 'es');
//$config['iwgb']['couriers-credentials'] = $iwgbCouriersCredentials;

//$app = new Slim\App(['settings' => $config]);
//
//session_start();
//
//// set up DB and templating
//
//$container = $app->getContainer();

$container['db'] = function ($c) {
    $db = $c['legacy']['db'];
    $pdo = new PDO('mysql:host=' . $db['host'] . ';dbname=' . $db['dbname'] . ';charset=utf8',
        $db['user'], $db['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};

//$container['twilio'] = function ($c) {
//    return new Twilio\Rest\Client($c['settings']['twilio']['sid'], $c['settings']['twilio']['token']);
//};
//
//$container['csrf'] = function ($c) {
//    return new Slim\Csrf\Guard;
//};

//$container['view'] = function ($container) {
//    $templates = __DIR__ . '/templates/';
//    $cache = __DIR__ . '/cache/';
//    $debug = false;
//    // $debug = true;
//    $view = new Slim\Views\Twig($templates, compact('cache', 'debug'));
//    $view->getEnvironment()->addGlobal('_get', $_GET);
//    $view->getEnvironment()->addGlobal('csrfKeys', [
//        'name'  => $container['csrf']->getTokenNameKey(),
//        'value' => $container['csrf']->getTokenValueKey(),
//    ]);
//
//    if ($debug) {
//        $view->addExtension(new \Slim\Views\TwigExtension(
//            $container['router'],
//            $container['request']->getUri()
//        ));
//        $view->addExtension(new \Twig_Extension_Debug());
//    }
//    return $view;
//};

//$container['notFoundHandler'] = function ($c) {
//    return function ($request, $response) use ($c) {
//        return $c->view->render($response, '404.html.twig')->withStatus(404);
//    };
//};

$container->view->getEnvironment()->addGlobal('csrfKeys', [
    'name'  => $container['csrf']->getTokenNameKey(),
    'value' => $container['csrf']->getTokenValueKey(),
]);

// filters

$f_timeAgo = new Twig_SimpleFilter('timeago', function ($s) {
    $timeAgo = new Westsworld\TimeAgo();
    return $timeAgo->inWords($s);
});
$container['view']->getEnvironment()->addFilter($f_timeAgo);

$f_nicedate = new Twig_SimpleFilter('nicedate', function ($s) {
    return date('j F o', strtotime($s));
});
$container['view']->getEnvironment()->addFilter($f_nicedate);

$f_urlencode = new Twig_SimpleFilter('urlencode', function($s) {
    return urlencode($s);
});
$container['view']->getEnvironment()->addFilter($f_urlencode);

$f_addslashes = new Twig_SimpleFilter('addslashes', function ($s) {
    return addslashes($s);
});
$container['view']->getEnvironment()->addFilter($f_addslashes);

$f_removeentities = new Twig_SimpleFilter('removequotes', function ($s) {
    return str_replace(array("'", "\""), '', $s);
});
$container['view']->getEnvironment()->addFilter($f_removeentities);

$f_uniqid = new Twig_SimpleFilter('uniqid', function ($s) {
    return uniqid();
});
$container['view']->getEnvironment()->addFilter($f_uniqid);

// middleware

$m_accesscontrol = function (Request $request, Response $response, $next) use ($container) {
    $callback = $request->getUri()->getPath();
    if ($callback != '/arms/login' && !isLoggedIn($container->session)) {
        $query = urlencode($request->getUri()->getQuery());
        if ($query != '') {
            $query = '&q=' . $query;
        }
        return $response->withRedirect("/arms/login?e=To view this page, you must log in.&callback=$callback$query", 302);
    } else {
        return $next($request, $response);
    }
};

// routes

//$app->get('/', function (Request $request, Response $response) {
//    $header = getPinnedPost($this->db, false, "AND b.type = 'posts'")->fetch();
//    return $this->view->render($response, 'home.html.twig', [
//        'header'        => $header,
//        'stories'       => getPosts($this->db, 3, 0, "AND p.id <> :header AND b.type = 'posts'", array(
//            ':header'       => $header['id'],
//        ))->fetchAll(),
//        'branches'      => getBranchesData(),
//        'dynamicContent'=> getDynamicPageData('home'),
//    ]);
//});

// custom

$app->get('/boycottsenatehouse[/]', function (Request $request, Response $response) {

    $categories = [
        'academics'     => 'user-graduate',
        'politicians'   => 'landmark',
        'organisations' => 'users',
        'public figures'=> 'user',
    ];
    $supporters = [];
    foreach ($categories as $category => $icon) {
        $csv = \League\Csv\Reader::createFromPath(__DIR__ . "/config/$category.csv");
        $csv->setHeaderOffset(0);
        $supporters[$category] = iterator_to_array($csv->getRecords());
    }


    return $this->view->render($response, 'boycott.html.twig', [
        'data'      => getJSON('boycott'),
        'pagetitle' => 'Boycott Senate House',
        'supporters'=> $supporters,
        'categories'=> $categories,
    ]);
});

$app->get('/boycottsenatehouse/join[/]', function (Request $request, Response $response) {
    return $response->withRedirect('https://iwgb.typeform.com/to/EBnlWo?sl=y');
});

// CMS functionality

$app->get('/post/{id}/{shortlink}', function (Request $request, Response $response, $args) {
    $post = getValidatePost($this->db, $args['id'], $args['shortlink']);
    if ($post->rowCount() == 0) {
        return notFoundHandler($this, $request, $response);
    } else {

        $featured = getPinnedPost($this->db, false, "AND b.type = 'posts'")->fetch();
        $stories = getPosts($this->db, 5, 0, "AND p.id <> :header AND p.id <> :featured AND b.type = 'posts'", array(
            ':header'   => $args['id'],
            ':featured' => $featured['id'],
        ))->fetchAll();
        return $this->view->render($response, 'article.html.twig', [
            'post'      =>  $post->fetch(),
            'featured'  =>  $featured,
            'stories'   =>  $stories,
        ]);
    }
});

$app->group('/feed', function() {

    $this->get('/{blog}/{page}', function (Request $request, Response $response, $args) {
        if (!isValidBlog($this->db, $args['blog'])) {
            return notFoundHandler($this, $request, $response);
        } else {
            if ($args['blog'] != 'all') {
                $header = getPinnedPost($this->db, $args['blog'], "AND b.type = 'posts'")->fetch();
            } else {
                $header = getPinnedPost($this->db, false, "AND b.type = 'posts'")->fetch();
            }
            $params = array();
            $sql = 'AND p.id <> :header';
            $params[':header'] = $header['id'];
            if ($args['blog'] != 'all') {
                $sql = appendQueryString($sql, 'AND b.name = :blog');
                $params[':blog'] = $args['blog'];
            }
            $sql = appendQueryString($sql, "AND b.type = 'posts'");
            $offset = (int)$args['page'] * 12;
            return $this->view->render($response, 'feed.html.twig', [
                'header'    => $header,
                'stories'   => getPosts($this->db, 12, $offset, $sql, $params)->fetchAll(),
                'page'      => (int)$args['page'],
                'blog'      => $args['blog'],
                'blogmeta'  => getBlogNames($this->db, $args['blog']),
            ]);
        }
    });

    $this->get('/{blog}', function (Request $request, Response $response, $args) {
        return $response->withRedirect('/feed/' . $args['blog'] . '/0');
    });

    $this->get('', function (Request $request, Response $response) {
        return $response->withRedirect('/feed/all/0');
    });
});

// Join

$app->group('/legacyjoin', function() {

    $this->get('', function (Request $request, Response $response) {
        return $this->view->render($response, 'join.html.twig', [
            'branches'  => getBranchesData(),
            'pagetitle' => 'Join us',
        ]);
    });

    $this->post('', function (Request $request, Response $response) {
        $post = $request->getParsedBody();
        try {
            $json = json_encode($request->getParsedBody());
        } catch (Exception $e) {
            return $response->withStatus(400);
        }

        if (verifyApplication($post) && verifyCaptcha($this->get('legacy')['recaptcha'], $post['g-recaptcha-response'])) {
            $id = uniqid();
            $q = $this->db->prepare('INSERT INTO members (id, json, branch, membership) VALUES (:id, :json, :branch, :membership);');
            $q->execute(array(
                ':id'           => $id,
                ':json'         => $json,
                ':branch'       => $post['branch'],
                ':membership'   => $post['cost'],
            ));
            $key = generateKey($this->db, $id, 'sms');
            sendTwilio($this->twilio, $post['mobile'], "Your verification key for your IWGB application (ref $id) is: $key");
            return $response->withRedirect("/join/verify/$id");
        } else {
            return $response->withRedirect($request->getQueryParam('callback'));
        }
    });

    $this->group('/verify', function() {

        $this->map(['GET', 'POST'], '/{id}/verify',  function (Request $request, Response $response, $args) {
            $data = array();
            if ($request->isGet()) {
                if (!empty($request->getQueryParam('k')) && !empty($request->getQueryParam('t'))) {
                    $data['keystr'][0] = $request->getQueryParam('k');
                    $data['type'][0] = $request->getQueryParam('t');
                } else {
                    return notFoundHandler($this, $request, $response);
                }
            } else {
                $data = $request->getParsedBody();
            }
            if (getUnverifiedKeys($this->db, $args['id'])) {
                if (isset($data['keystr'])) {
                    // check each provided verification attempt
                    for ($i = 0; $i < count($data['keystr']); $i++) {
                        try {
                            verifyKey($this->db, $args['id'], $data['keystr'][$i], $data['type'][$i]);
                        } catch (Exception $e) {
                            return notFoundHandler($this, $request, $response);
                        }
                    }
                }
            }
            if (checkApplicationVerified($this->db, $args['id'])) {
                $application = getApplicationData($this->db, $args['id']);
                $settings = $this->get('legacy');
                confirmApplication($this->db, $args['id'], $application['json'], $settings['mailgun'], $settings['iwgb']['joindata']);
                return $response->withRedirect('/join/verify/' . $args['id'] . '/success');
            } else {
                return $response->withRedirect('/join/verify/' . $args['id']);
            }
        });

        $this->get('/{id}/success',  function (Request $request, Response $response, $args) {
            if(checkApplicationVerified($this->db, $args['id'])) {
                $application = getApplicationData($this->db, $args['id']);
                return $this->view->render($response, 'verified.html.twig', [
                    'id'            => $args['id'],
                    'pagetitle'     => 'Join us',
                    'application'   => $application,
                    'membership'    => getBranchData($application['branch'])['costs'][$application['membership']],
                ]);
            }
        });

        $this->get('/{id}', function (Request $request, Response $response, $args) {
            $exists = doesApplicationExist($this->db, $args['id']);
            if (!$exists) {
                return notFoundHandler($this, $request, $response);
            } else {
                $keys = getUnverifiedKeys($this->db, $args['id']);
                return $this->view->render($response, 'verify.html.twig', [
                    'id'        => $args['id'],
                    'keys'      => $keys,
                    'pagetitle' => 'Join us',
                    'csrfValues'    => [
                        'name' => $request->getAttribute($this->csrf->getTokenNameKey()),
                        'value'=> $request->getAttribute($this->csrf->getTokenValueKey()),
                    ],
                ]);
            }
        });
    });

    $this->get('/{branch}', function (Request $request, Response $response, $args) {
        $branch = getBranchData($args['branch']);
        if ($branch) {
            return $this->view->render($response, 'form.html.twig', [
                'branch'    => $branch,
                'join'      => getJoinData(),
                'pagetitle' => 'Join ' . $branch['display'],
                'csrfValues'    => [
                    'name' => $request->getAttribute($this->csrf->getTokenNameKey()),
                    'value'=> $request->getAttribute($this->csrf->getTokenValueKey()),
                ],
            ]);
        } else {
            return notFoundHandler($this, $request, $response);
        }
        return $this->view->render();
    });
});

$app->get('/page/{category}/{page}', function (Request $request, Response $response, $args) {
    $q = $this->db->prepare("SELECT p.title,
        p.shortlink,
        p.content,
        p.header_img,
        b.friendly_name,
        b.name
        FROM posts p INNER JOIN blogs b ON p.blog = b.name
        WHERE p.shortlink = :post
        AND p.language = :language
        GROUP BY p.id;"
    );
    $q->execute(array(
        ':post'     => $args['page'],
        ':language' => $response->getHeader('Content-Language')[0],
    ));
    if ($q->rowCount() == 0) {
        return notFoundHandler($this, $request, $response);
    } else {
        $page = $q->fetch();
    }
    return $this->view->render($response, 'static.html.twig', [
        'nav'   => getPagesInCategory($this->db, $args['category']),
        'page'  => $page,
    ]);
});

$app->group('/arms/verify', function() {

    $this->get('/', function (Request $request, Response $response, $args) {
        if(empty($request->getQueryParam('k'))) {
            return $response->withRedirect('/arms/login?e=Your verification credentials were incorrect.');
        }
        $key = $request->getQueryParam('k');
        if(verifyArmsKey($this->db, $key)) {
            logEvent($this->db, 'verify-click', $key, 'valid', $request->getAttribute('ip_address'));
            return $this->view->render($response, 'arms-verify.html.twig', [
                'key'       => $key,
                'pagetitle' => 'Verify account',
                'csrfValues'    => [
                    'name' => $request->getAttribute($this->csrf->getTokenNameKey()),
                    'value'=> $request->getAttribute($this->csrf->getTokenValueKey()),
                ],
            ]);
        } else {
            logEvent($this->db, 'verify-click', $key, 'invalid', $request->getAttribute('ip_address'));
            return $response->withRedirect('/arms/login?e=Your verification credentials were incorrect.');
        }
    });

    $this->post('/', function (Request $request, Response $response, $args) {
        $post = $request->getParsedBody();
        if (!empty($post['key']) && !empty($post['email']) && !empty($post['pass'])) {
            if (verifyArmsKey($this->db, $post['key'], $post['email'])) {
                logEvent($this->db, 'verify', $post['key'], 'success', $post['email']);
                register($this->db, $post['email'], $post['pass']);
                removeArmsKey($this->db, $post['key']);
                return $response->withRedirect('/arms/login?m=Account verified successfully.');
            } else {
                logEvent($this->db, 'verify', $post['key'], 'unauthorised', $post['email']);
                removeArmsKey($this->db, $post['key']);
                return $response->withRedirect('/arms/login?e=Your verification credentials were incorrect.');
            }
        } else {

            return $response->withRedirect('/arms/login?e=Your verification credentials were incorrect.');
        }
    });
});


$app->group('/arms', function() {

    $this->get('',  function (Request $request, Response $response, $args) {

    });

    $this->get('/login', function (Request $request, Response $response) {
        $data = array();
        return $this->view->render($response, 'arms-login.html.twig', [
            'csrfValues'    => [
                'name' => $request->getAttribute($this->csrf->getTokenNameKey()),
                'value'=> $request->getAttribute($this->csrf->getTokenValueKey()),
            ],
        ]);
    });

    $this->post('/login', function (Request $request, Response $response) {
        $post = $request->getParsedBody();
        $login = logIn($this->db, $post, $this->session, $request->getAttribute('ip_address'));
        if ($login === true) {
            $url = '/arms/feed';
            if (!empty($post['callback']) && $post['callback'] != '/arms') {
                $url = $post['callback'];
            }
            if (!empty($post['query'])) {
                $url .= '?' . $post['query'];
            }
            logEvent($this->db, 'login', 'iwgb', 'success');
            return $response->withRedirect($url);
        } else {
            return $response->withRedirect("/arms/login?e=$login");
        }
    });

    $this->get('/logout', function (Request $request, Response $response) {
        logOut($this->db, $this->session);
        return $response->withRedirect('/arms/login?m=You have been logged out.');
    });

    $this->get('/feed/{blog}/{page}',  function (Request $request, Response $response, $args) {
        $params = array();
        $sql = '';
        if ($args['blog'] != 'all') {
            if (isValidBlogType($this->db, $args['blog'])) {
                $sql = 'AND b.type = :type';
                $params[':type'] = $args['blog'];
            } else if (isValidBlog($this->db, $args['blog'])) {
                $sql = 'AND b.name = :blog';
                $params[':blog'] = $args['blog'];
            } else {
                return notFoundHandler($this, $request, $response);
            }
        }
        $offset = (int)$args['page'] * 12;
        $blogmeta = getBlogNames($this->db, $args['blog']);
        return $this->view->render($response, 'arms-feed.html.twig', [
            'posts'     => getPosts($this->db, 12, $offset, $sql, $params)->fetchAll(),
            'page'      => (int)$args['page'],
            'blogmeta'  => $blogmeta,
            'blogs'     => getBlogNames($this->db, false, 'posts'),
            'categories'=> getBlogNames($this->db, false, 'pages'),
            'pagetitle' => $blogmeta['friendly_name'],
        ]);
    });

    $this->get('/feed/{blog}', function (Request $request, Response $response, $args) {
        return $response->withRedirect('/arms/feed/' . $args['blog'] . '/0');
    });

    $this->get('/feed', function (Request $request, Response $response) {
        return $response->withRedirect('/arms/feed/posts/0');
    });

    $this->get('/publish', function (Request $request, Response $response, $args) {
        $overwrite = array(
            'do'        => false,
            'method'    => 'Create',
        );
        $select = '';
        if (!empty($request->getQueryParam('o'))) {
            $post = getValidatePost($this->db, $request->getQueryParam('o'));
            if($post->rowCount() == 1) {
                $overwrite = array_merge($post->fetch(), array(
                    'do'        => true,
                    'method'    => 'Edit'
                ));
                $select = $overwrite['blog'];
            }
        }
        $currentUser = getCurrentUser($this->session)['email'];
        return $this->view->render($response, 'arms-publish.html.twig', [
            'overwrite'     => $overwrite,
            'users'         => getUsers($this->db),
            'currentuser'   => $currentUser,
            'pagetitle'     => $overwrite['method'] . ' post',
            'admin'         => isUser($this->db, $currentUser, true),
            'blogs'         => getBlogNames($this->db, false, 'posts'),
            'categories'    => getBlogNames($this->db, false, 'pages'),
            'select'        => $select,
            'csrfValues'    => [
                'name' => $request->getAttribute($this->csrf->getTokenNameKey()),
                'value'=> $request->getAttribute($this->csrf->getTokenValueKey()),
            ],
        ]);
    });

    $this->post('/publish', function (Request $request, Response $response, $args) {
        $post = $request->getParsedBody();

        if (!empty($post['content'])) {
            $content = $post['content'];
        } else {
            return $response->withRedirect('/arms/feed/all/0?e=You attempted to make a post with no content');
        }

        $overwrite = false;
        $original = array();
        if (!empty($post['overwrite'])) {
            $original = getValidatePost($this->db, $post['overwrite']);
            if ($original->rowCount() == 1) {
                $overwrite = $post['overwrite'];
            } else {
                return $response->withRedirect('/arms/feed/all/0?e=The post you are attempting to edit does not exist');
            }
        }

        if (!empty($post['author']) && isUser($this->db, $post['author'])) {
            $author = $post['author'];
        } else {
            return redirectOnPublishError($response, 'That author does not exist', $overwrite, $content);
        }

        if (!empty($post['blog']) && isValidBlog($this->db, $post['blog'])) {
            $blog = $post['blog'];
        } else {
            return redirectOnPublishError($response, 'That blog does not exist', $overwrite, $content);
        }

        if (!empty($post['title'])) {
            if (strlen($post['title']) <= 200) {
                $title = $post['title'];
            } else {
                return redirectOnPublishError($response, 'You must supply a title that is less than 200 characters in length', $overwrite, $content);
            }
        } else {
            return redirectOnPublishError($response, 'You attempted to make an untitled post', $overwrite, $content);
        }

        if (!empty($post['shortlink']) && isValidBlogType($this->db, 'pages')) {
            if (isUnusedShortlink($this->db, $post['shortlink']) || $overwrite) {
                $shortlink = $post['shortlink'];
            } else {
                return redirectOnPublishError($response, 'Your shortlink has already been used', $overwrite, $content);
            }
        } else {
            $shortlink = generateShortlink($title);
        }

        if (!empty($post['language'])) {
            $language = $post['language'];
        } else {
            return redirectOnPublishError($response, 'Your post must have a defined langauge', $overwrite, $content);
        }

        if (!empty($request->getUploadedFiles()['header_img']) && $request->getUploadedFiles()['header_img']->getClientFilename() != '') {
            $header_img = saveFileToBucket($request->getUploadedFiles()['header_img']);
            if (!$header_img) {
                return redirectOnPublishError($response, 'Your header image failed to upload', $overwrite, $content);
            }
        } else {
            $header_img = '';
        }

        if ($overwrite) {
            if (empty($header_img)) {
                $header_img = $original->fetch()['header_img'];
            }
            $q = $this->db->prepare('UPDATE posts
                SET author = :author,
                blog = :blog,
                content = :content,
                title = :title,
                header_img = :header_img
                WHERE id = :id'
            );
            $q->execute(array(
                ':id'           => $overwrite,
                ':blog'         => $blog,
                ':content'      => $content,
                ':author'       => $author,
                ':title'        => $title,
                ':header_img'   => $header_img,
            ));
            logEvent($this->db, 'edit', $overwrite);
        } else {
            $q = $this->db->prepare('INSERT INTO posts (id, author, blog, content, language, shortlink, posted_by, title, header_img) 
                VALUES (:id, :author, :blog, :content, :language, :shortlink, :posted_by, :title, :header_img);'
            );
            $postID = uniqid();
            $q->execute(array(
                ':id'           => $postID,
                ':author'       => $author,
                ':blog'         => $blog,
                ':content'      => $content,
                ':language'     => $language,
                ':shortlink'    => $shortlink,
                ':posted_by'    => getCurrentUser($this->session)['email'],
                ':title'        => $title,
                ':header_img'   => $header_img,
            ));
            logEvent($this->db, 'publish', $postID);
        }
        return $response->withRedirect('/arms/feed/all/0?m=Your post has been published!');
    });

    $this->post('/upload', function (Request $request, Response $response) {
        if (!empty($request->getUploadedFiles()['file']) && $request->getUploadedFiles()['file']->getClientFilename() != '') {
            try {
                $file = saveFileToBucket($request->getUploadedFiles()['file'], false);
            } catch (Exception $e) {
                return $response->withRedirect('/arms/upload?e=Your file failed to upload (' . $e->getMessage() . ')');
            }
            if (!$file) {
                return $response->withRedirect('/arms/upload?e=Your file failed to upload');
            }
        } else {
            return $response->withRedirect('/arms/upload?e=Your file failed to upload');
        }
        logEvent($this->db, 'upload', $file);
        return $response->withRedirect("/arms/upload?m=File uploaded successfully&f=$file");
    });

    $this->get('/upload', function (Request $request, Response $response, $args) {
        $prevFileName = '';
        if (!empty($request->getQueryParam('f'))) {
            $prevFileName = '/img/bucket/' . $request->getQueryParam('f');
        }
        return $this->view->render($response, 'arms-upload.html.twig', [
            'pagetitle' => 'Upload media',
            'result'    => $prevFileName,
            'csrfValues'    => [
                'name' => $request->getAttribute($this->csrf->getTokenNameKey()),
                'value'=> $request->getAttribute($this->csrf->getTokenValueKey()),
            ],
        ]);
    });

    $this->group('/{id}', function() {

        $this->get('/edit', function (Request $request, Response $response, $args) {
            return $response->withRedirect('/arms/publish?o=' . $args['id']);
        });

        $this->get('/delete', function (Request $request, Response $response, $args) {
            if (getValidatePost($this->db, $args['id'])->rowCount() == 1) {
                $q = $this->db->prepare('UPDATE posts
                    SET deleted = 1
                    WHERE id = :id'
                );
                $q->execute(array(
                    ':id' => $args['id'],
                ));
                logEvent($this->db, 'delete', $args['id']);
                return redirectToCallbackUrl($response, '/arms/feed/all/0', array(
                    'm' => 'Post deleted successfully',
                    'u' => '/arms/' . $args['id'] . '/restore',
                ), $request->getQueryParam('callback'));
            } else {
                logEvent($this->db, 'delete', $args['id'], 'failed: not found');
                return redirectToCallbackUrl($response, '/arms/feed/all/0', array(
                    'e' => 'You cannot delete that post, it does not exist',
                ), $request->getQueryParam('callback'));
            }
        });

        $this->get('/restore', function (Request $request, Response $response, $args) {
            if (getValidatePost($this->db, $args['id'], false, true)->rowCount() == 1) {
                $q = $this->db->prepare('UPDATE posts
                    SET deleted = 0
                    WHERE id = :id'
                );
                $q->execute(array(
                    ':id' => $args['id'],
                ));
                logEvent($this->db, 'restore', $args['id']);
                return redirectToCallbackUrl($response, '/arms/feed/all/0', array(
                    'm' => 'Post restored successfully',
                    'u' => '/arms/' . $args['id'] . '/delete',
                ), $request->getQueryParam('callback'));
            } else {
                logEvent($this->db, 'restore', $args['id'], 'failed: not found');
                return redirectToCallbackUrl($response, '/arms/feed/all/0', array(
                    'e' => 'Sorry, this post has been permanently deleted and cannot be restored',
                ), $request->getQueryParam('callback'));
            }
        });
    });

    $this->group('/dynamic', function() {
        $this->get('', function (Request $request, Response $response, $args) {
            return $this->view->render($response, 'arms-dynamic-menu.html.twig', [
                'pagetitle' => 'Dynamic pages',
                'pages'     => getDynamicPagesData(),
            ]);
        });

        $this->get('/{page}', function (Request $request, Response $response, $args) {
            if (!isValidDynamicPage($args['page'])) {
                return notFoundHandler($this, $request, $response);
            }
            return $this->view->render($response, 'arms-dynamic-page.html.twig', [
                'pagetitle' => 'Edit ' . $args['page'],
                'dynamic'   => getDynamicPageData($args['page']),
                'static'    => getDynamicElementsData(),
                'page'      => $args['page'],
                'csrfValues'    => [
                    'name' => $request->getAttribute($this->csrf->getTokenNameKey()),
                    'value'=> $request->getAttribute($this->csrf->getTokenValueKey()),
                ],
            ]);
        });

        $this->post('/{page}', function (Request $request, Response $response, $args) {
            $post = $request->getParsedBody();

            $page = array();
            $definitions = getDynamicElementsData();
            foreach ($post as $key => $value) {
                if (!in_array($key, [
                    $this->csrf->getTokenNameKey(),
                    $this->csrf->getTokenValueKey(),
                ])) {
                    echo '  not in array  \n\n';
                    $definitionFields = $definitions[$value['type']]['fields'];
                    $page[] = getField($value, $definitionFields);
                }
            }
            $save['pages'][$args['page']] = array(
                'name'      => $args['page'],
                'elements'  => $page,
            );
            $save = json_encode($save, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            file_put_contents(__DIR__ . '/config/pages.json', $save);
            return $response->withRedirect('/arms/dynamic?m=Changes saved');
        });
    });

})->add($m_accesscontrol);

//$app->run();

// Database

function appendQueryString($q, $s) {
    return "$q $s";
}

function getPosts($db, $lim = -1, $off = 0, $sql = false, $params = array(), $deleted = false) {
    $q = "SELECT
          p.id,
          p.shortlink,
          p.timestamp,
          p.title,
          p.content,
          p.language,
          p.header_img,
          p.author,
          p.posted_by,
          p.blog,
          b.friendly_name,
          b.friendly_singular,
          u.name,
          u.photo_id
        FROM posts p INNER JOIN blogs b ON p.blog = b.name
          INNER JOIN users u ON p.author = u.email
        WHERE TRUE";
    if ($deleted === false) {
        $q = appendQueryString($q, 'AND p.deleted = 0');
    }
    if ($sql) {
        $q = appendQueryString($q, $sql);
    }
    $q = appendQueryString($q, 'ORDER BY p.timestamp DESC');
    if ($lim >= 0) {
        $q = appendQueryString($q, "LIMIT $lim");
    }
    $q = appendQueryString($q, "OFFSET $off;");
    $q = $db->prepare($q);
    if(empty($params)) {
        $q->execute();
    } else {
        $q->execute($params);
    }
    return $q;
}

function getPost($db, $id) {
    return getPosts($db, 1, 0, 'AND p.id = :id', array(
        ':id' => $id,
    ))->fetch();
}

function getValidatePost($db, $id, $shortlink = false, $deleted = false) {
    $sql = 'AND p.id = :id';
    $params = array(
        ':id' => $id,
    );
    if ($shortlink) {
        $sql = appendQueryString($sql, 'AND p.shortlink = :shortlink');
        $params[':shortlink'] = $shortlink;
    }
    return getPosts($db, 1, 0, $sql, $params, $deleted);
}

function isValidBlog($db, $blog) {
    $q = $db->prepare("SELECT b.name
        FROM blogs b
        WHERE b.name = :blog");
    $q->execute(array(
        ':blog' => $blog,
    ));
    return $q->rowCount() == 1 || $blog == 'all';
}


function getPinnedPosts($db, $lim = -1, $sql = '', $params = array()) {
    $sql = appendQueryString($sql, "AND p.header_img <> ''");
    return getPosts($db, $lim, 0, $sql, $params);
}

function getPinnedPost($db, $blog = false, $sql = '') {
    $params = array();
    if ($blog) {
        $sql = appendQueryString($sql, 'AND p.blog = :blog AND p.header_img <> ""');
        $params[':blog'] = $blog;
    } else {
        $sql = appendQueryString($sql, 'AND p.header_img <> ""');
    }
    return getPosts($db, 1, 0, $sql, $params);
}

function getBlogNames($db, $blog = false, $type = false) {
    if ($blog == 'all') {
        return array(
            'friendly_name'     => 'All stories',
            'friendly_singular' => 'Story',
            'name'              => $blog,
        );
    } else if ($blog == 'posts' || $blog == 'pages') {
        return array(
            'friendly_name'     => "All $blog",
            'friendly_singular' => substr($blog, 0, -1),
            'name'              => $blog,
        );
    } else if (!$blog) {
        $q = 'SELECT friendly_name,
            friendly_singular,
            name
            FROM blogs';
        if ($type) {
            $q = appendQueryString($q, "WHERE type = :type");
        }
        $q = $db->prepare($q);
        $q->execute(array(
            ':type' => $type,
        ));
        return $q->fetchAll();
    } else {
        $q = $db->prepare ("SELECT friendly_name,
            friendly_singular,
            name
            FROM blogs
            WHERE name = :blog");
        $q->execute(array(
            ':blog' => $blog,
        ));
        return $q->fetch();
    }
}

// JSON

function getJoinData() {
    return getJSON('join');
}

function getBranchesData() {
    return getJSON('branches');
}

function getDynamicPagesData() {
    return getJSON('pages');
}

function getDynamicElementsData() {
    return json_decode(file_get_contents(__DIR__ . "/legacy/js/elements.json"), true)['elements'];
}

function getArmsToolsData() {
    return json_decode(file_get_contents(__DIR__ . "/legacy/js/arms/tools.json"), true)['tools'];
}

function getJSON($file) {
    return json_decode(file_get_contents(__DIR__ . "/legacy/config/$file.json"), true)[$file];
}

function getBranchData($branch) {
    $branches = getBranchesData();
    if (isset($branches[$branch])) {
        return $branches[$branch];
    } else {
        return false;
    }
}

function getDynamicPageData($page) {
    $pages = getDynamicPagesData();
    if (isset($pages[$page])) {
        return $pages[$page];
    } else {
        return false;
    }
}


function getField($field, $definitionFields) {
    foreach ($field as $key => $value) {
        switch ($key) {
            case 'type':
                $element['type'] = $value;
                break;

            case 'columns':
                $items = array();
                $element['fields']['columns'] = array(
                    'name' => 'columns',
                    'type' => 'column-list',
                );
                foreach ($value as $name => $column) {
                    $element['fields']['columns']['items'][] = array_merge(
                        array('name' => $name),
                        getField($column, $definitionFields['columns']['fields'])
                    );
                }
                break;

            default:

                switch ($definitionFields[$key]['role']) {
                    case 'meta':
                        $element[$key] = $value;
                        break;

                    case 'content':
                        switch ($definitionFields[$key]['type']) {
                            case 'repeat':
                                $thisElement['fields'][$key] = parseContentField($value);
                                break;

                            case 'image':
                                $element['fields'][$key] = array(
                                    'name'  => $key,
                                    'type'  => 'image',
                                    'src'   => $value,
                                );
                                break;

                            default:
                                $element['fields'][$key] = array(
                                    'name'      => $key,
                                    'type'      => $definitionFields[$key]['type'],
                                    'content'   => $value,
                                );
                        }
                }
        }
    }
    return $element;
}

// verify

function generateKey($db, $id, $type) {
    $key = rand(1111, 9999);
    $q = $db->prepare('INSERT INTO verify (id, type, keystr) VALUES (:id, :type, :key);');
    $q->execute(array(
        ':id'   => $id,
        ':type' => $type,
        ':key'  => $key,
    ));
    return $key;
}

function getUnverifiedKeys($db, $id) {
    $q = $db->prepare("SELECT type
        FROM verify
        WHERE id = :id
        AND verified = 'no'"
    );
    $q->execute(array(
        ':id'   => $id,
    ));
    if ($q->rowCount() == 0) {
        return false;
    } else {
        return $q->fetchAll();
    }
}

function doesApplicationExist($db, $id) {
    $q = $db->prepare("SELECT id 
        FROM members
        WHERE id = :id"
    );
    $q->execute(array(
        ':id' => $id,
    ));
    return $q->rowCount() == 1;
}

function checkApplicationVerified($db, $id) {
    if (doesApplicationExist($db, $id)) {
        $q = $db->prepare("SELECT verified
            FROM verify
            WHERE id = :id
            AND verified = 'no'"
        );
        $q->execute(array(
            ':id' => $id,
        ));
        return $q->rowCount() == 0;
    }
}

function verifyKey($db, $id, $key, $type) {
    $q = $db->prepare("UPDATE verify
        SET verified = 'yes'
        WHERE id = :id
        AND keystr = :key
        AND type = :type
        AND verified = 'no';"
    );
    $q->execute(array(
        ':id'   => $id,
        ':key'  => $key,
        ':type' => $type,
    ));
    if ($q->rowCount() == 0) {
        return false;
    } else {
        return true;
    }
}

function verifyApplication($post) {
    $join = json_decode(file_get_contents(__DIR__ . '/config/join.json'), true)['join'];
    $valid = true;
    $reason = '';
    foreach ($join as $section) {
        switch ($section['type']) {
            case 'inline':
                foreach ($section['fields'] as $line) {
                    $validity = verifyFields($line, $post);
                    if ($validity !== true) {
                        $reason = $validity;
                        $valid = false;
                    }
                }
                break;
            case 'vertical':
                $validity = verifyFields($section['fields'], $post);
                if ($validity !== true) {
                    $reason = $validity;
                    $valid = false;
                }
                break;
        }
        if (!$valid) {
            break;
        }
    }
    if ($valid) {
        return $valid;
    } else {
        return $reason;
    }
}

function verifyFields($fields, $post) {
    $valid = true;
    $reason = '';
    foreach ($fields as $field) {
        if (isset($field['required'])) {
            if ($field['required']) {
                if (!isset($post[$field['name']])) {
                    $valid = false;
                    $reason = $field['display'];
                }
            }
        }
    }
    if ($valid) {
        return true;
    } else {
        return $reason;
    }
}

function getApplicationData($db, $id) {
    $q = $db->prepare("SELECT m.json, m.branch, m.membership
        FROM members m
        WHERE id = :id;"
    );
    $q->execute(array(
        ':id' => $id,
    ));
    return $q->fetch();
}

function confirmApplication($db, $id, $json, $mailgun, $emails) {
    $q = $db->prepare("UPDATE members
        SET confirmed = b'1'
        WHERE id = :id;"
    );
    $q->execute(array(
        ':id'   => $id,
    ));

    foreach ($emails as $email) {
        sendEmail($mailgun, $email, "New Member Application $id", json_encode(json_decode($json, true), JSON_PRETTY_PRINT));
    }

    return !($q->rowCount() == 0);
}

// arms verify

function verifyArmsKey($db, $key, $email = false) {
    $sql = 'SELECT email
        FROM pw_keys
        WHERE keystr = :keystr
        AND timestamp + INTERVAL 1 DAY > NOW()';
    $params = array(
        ':keystr' => $key,
    );
    if ($email) {
        $sql = appendQueryString($sql, 'AND email = :email');
        $params[':email'] = $email;
    }
    $q = $db->prepare($sql);
    $q->execute($params);
    return $q->rowCount() == 1;
}

function removeArmsKey($db, $key) {
    $q = $db->prepare('DELETE FROM pw_keys
        WHERE keystr = :keystr'
    );
    $q->execute(array(
        ':keystr' => $key,
    ));
}

// pages

function getPagesInCategory($db, $category) {
    $q = $db->prepare('SELECT p.title, p.shortlink
        FROM posts p
        WHERE blog = :category;'
    );
    $q->execute(array(
        ':category' => $category,
    ));
    if ($q->rowCount() == 0) {
        return false;
    } else {
        return $q->fetchAll();
    }
}

function isValidBlogType($db, $type) {
    $q = $db->prepare('SELECT b.type
        FROM blogs b
        WHERE b.type = :type;'
    );
    $q->execute(array(
        ':type' => $type,
    ));
    return $q->rowCount();
}

function getUsers($db) {
    $q = $db->prepare('SELECT email, name, permissions
        FROM users;');
    $q->execute();
    return $q->fetchAll();
}

function isUser($db, $user, $admin = false) {
    $q = 'SELECT permissions 
        FROM users
        WHERE email = :user';
    if ($admin) {
        $q = appendQueryString($q, "AND permissions = 'admin'");
    }
    $q = $db->prepare($q);
    $q->execute(array(
        ':user' => $user,
    ));
    return $q->rowCount() == 1;
}

function isUnusedShortlink($db, $shortlink) {
    $q = $db->prepare("SELECT id 
        FROM posts
        WHERE shortlink = :shortlink"
    );
    $q->execute(array(
        ':shortlink' => $shortlink,
    ));
    return $q->rowCount() == 0;
}

function isValidDynamicPage($name) {
    foreach (getDynamicPagesData() as $page) {
        if ($page['name'] == $name) {
            return true;
        }
    }
    return false;
}

// application

function redirectOnPublishError($response, $message, $overwrite, $content) {
    $content = str_replace(array("\r\n", "\n", "\r"), '%%N%%', $content);
    $content = urlencode($content);
    if ($overwrite) {
        return $response->withRedirect("/arms/publish?e=$message&t=$content&o=$overwrite");
    } else {
        return $response->withRedirect("/arms/publish?e=$message&t=$content");
    }
}

function generateShortlink($title) {
    $shortlink = "";
    $titleWordArray = explode(" ", $title);
    if(count($titleWordArray) < 5) {
        foreach($titleWordArray as $word) {
            $slWord = preg_replace("/[^A-Za-z0-9 ]/", '', $word);
            $shortlink .= strtolower($slWord) . "-";
        }
    } else {
        for($i = 0; $i < 5; $i++) {
            $slWord = preg_replace("/[^A-Za-z0-9 ]/", '', $titleWordArray[$i]);
            $shortlink .= strtolower($titleWordArray[$i]) . "-";
        }
    }
    $shortlink = htmlspecialchars(substr($shortlink, 0, -1));
    if (strlen($shortlink) > 30) {
        $shortlink = substr($shortlink, 0, 30);
    }
    return $shortlink;
}

function saveFileToBucket($file, $image = true) {
    if ($image) {
        if (pathinfo($file->getClientFilename(), PATHINFO_EXTENSION) != 'jpg') {
            return false;
        }
        $ext = 'jpg';
    } else {
        $ext = pathinfo($file->getClientFilename(), PATHINFO_EXTENSION);
    }
    $id = uniqid();
    $file->moveTo(__DIR__ . "/img/bucket/$id.$ext");
    if ($image) {
        return $id;
    } else {
        return "$id.$ext";
    }
}

function sendTwilio($client, $to, $body, $from = 'IWGB') {
    switch(substr($to, 0, 1)) {
        case '0':
            $to = '+44' . substr($to, 1);
            break;
        case '7':
            $to = '+44' . $to;
            break;
        case '+':
            break;
        default:
            return false;
    }
    $client->messages->create($to, array(
        'from' => $from,
        'body' => $body,
    ));
}

function sendEmail($mailgun, $to, $subject, $body, $replyTo = 'office@iwgb.org.uk') {
    $apikey = $mailgun['key'];
    $url = "https://api:key-$apikey@api.mailgun.net/v3/mx.iwgb.org.uk/messages";
    $result = requestJSON($url, array(
        'from'      => "noreply@iwgb.org.uk",
        'to'        => $to,
        'subject'   => $subject,
        'text'      => $body,
        'h:Reply-To'=> $replyTo,
    ));
}

function verifyCaptcha($recaptcha, $s) {
    $result = requestJSON('https://www.google.com/recaptcha/api/siteverify', array(
        'secret' => $recaptcha['secret'],
        'response'  => $s,
    ));
    if ($result) {
        return $result['success'];
    } else {
        return false;
    }
}

function requestJSON($url, $data) {
    $stream = stream_context_create(array(
        'http' => array(
            'header'    => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'    => "POST",
            'content'   => http_build_query($data),
        ),
    ));
    try {
        return json_decode(file_get_contents($url, false, $stream), true);
    } catch (Exception $e) {
        return false;
    }
}

function redirectToCallbackUrl($response, $default, $params = array(), $callback = false) {
    $url = '';
    if ($callback) {
        $url .= $callback;
    } else {
        $url .= $default;
    }
    if (!empty($params)) {
        $url .= '?';
        foreach ($params as $param => $var) {
            $url .= "$param=$var&";
        }
        $url = substr($url, 0, -1);
    }
    error_log($url);
    return $response->withRedirect($url);
}

function logEvent($db, $type, $upon = '', $notes = '', $by = false) {
    if (!$by) {
        $by = 'unknown';
    }
    $q = $db->prepare('INSERT INTO events (id, type, act_upon, act_by, notes)
        VALUES (:id, :type, :upon, :by, :notes);'
    );
    $id = uniqid();
    $q->execute(array(
        ':id'   => $id,
        ':type' => $type,
        ':upon' => $upon,
        ':by'   => $by,
        ':notes'=> $notes,
    ));
    return $id;
}

// SLIM

function notFoundHandler($app, $request, $response) {
    return $app->get('notFoundHandler')($request, $response);
}

// auth
// hat-tip to panique/php-login-minimal

function isLoggedIn($session) {
    return ($session->get('loginStatus'));
}

function logIn($db, $post, $session, $ip) {
    $q = $db->prepare("SELECT id 
        FROM events
        WHERE type = 'login'
    AND act_by = :ip
    AND timestamp BETWEEN DATE_SUB(NOW() , INTERVAL 10 MINUTE) AND NOW()"
    );
    $q->execute(array(
        ':ip' => $ip,
    ));
    if ($q->rowCount() > 3) {
        logEvent($db, 'login', 'iwgb', 'too many attempts', $ip);
        return "You are only allowed 3 attempts every 10 minutes.";
    }
    if (empty($post['user']) || empty($post['pass'])) {
        return "You have not provided a username and/or password.";
    } else {
        $q = $db->prepare("SELECT email, name, pass
            FROM users
            WHERE email = :user");
        $q->execute(array(
            ':user' => $post['user'],
        ));
        if ($q->rowCount() == 1) {
            $user = $q->fetch();
            if (password_verify($post['pass'], $user['pass'])) {
                $session->clear()
                    ->set('user', $user['email'])
                    ->set('name', $user['name'])
                    ->set('loginStatus', true);
                if (password_needs_rehash($user['pass'], PASSWORD_DEFAULT)) {
                    $hash = password_hash($post['pass'], PASSWORD_DEFAULT);
                    $q = $db->prepare('UPDATE users SET pass = :pass WHERE email = :user');
                    $q->execute(array(
                        ':user' => $user['email'],
                        ':pass' => $hash,
                    ));
                }
                return true;
            } else {
                logEvent($db, 'login', 'iwgb', 'unauthorised', $ip);
                return "Your credentials could not be authorised";
            }
        } else {
            logEvent($db, 'login', 'iwgb', 'unauthorised', $ip);
            return "Your credentials could not be authorised";
        }
    }
}

function register($db, $email, $pass) {
    $q = $db->prepare('UPDATE users
        SET pass = :pass
        WHERE email = :email'
    );
    $q->execute(array(
        ':pass' => password_hash($pass, PASSWORD_DEFAULT),
        ':email'=> $email,
    ));
}

function logOut($db, $session) {
    logEvent($db, 'logout');
    $session::destroy();
}

function getCurrentUser($session) {
    if ($session->exists('user')) {
        return array(
            'email' => $session->get('user'),
            'name'  => $session->get('name'),
        );
    } else {
        return 'user';
    }
}

function getCurrentUserData($db, $session) {
    $q = $db->prepare('SELECT permissions, organisation
        FROM users
        WHERE email = :email'
    );
    $q->execute(array(
        ':email' => getCurrentUser($session)['email'],
    ));
    return $q->fetch();
}
