<?php

if (!($loader = @include __DIR__ . '/vendor/autoload.php')) {
    die(<<<EOT
You need to install the project dependencies using Composer:
$ wget http://getcomposer.org/composer.phar
OR
$ curl -s https://getcomposer.org/installer | php
$ php composer.phar install --dev
$ phpunit
EOT
    );
}


use Kimeo\GitHubGenerator;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;

Debug::enable();

$dotenv = new Dotenv();
$dotenv->load(__DIR__ . '/.env');

if ('POST' === $_SERVER['REQUEST_METHOD']) {
    $request = Request::createFromGlobals();
    $postValues = $request->request->all();
    $args = constructArguments($postValues);

    $user = $_ENV['GITHUB_LOGIN'];
    $password = $_ENV['GITHUB_PASSWORD'];
    $owner = $_ENV['GITHUB_OWNER'];
    $project = $_ENV['GITHUB_REPOSITORY'];

    $from = new DateTime($args['from']);
    $to = new DateTime($args['to']);
    $branches = $args['branches'];

    $generator = new GitHubGenerator($user, $password, $owner, $project);
    
    try {
        $generator->generate($from, $to, $branches);
        (new Filesystem())->dumpFile('report.md', $generator->getReport());

        $success = "<a href='report.md' download>Download here</a>"
            . "<pre><code id='text'>"
            . $generator->getReport()
            . "</code></pre>"
            . "<div id='display'></div>"
        ;
    } catch (\Exception $e) {
        echo $e->getMessage();
    }
}

function constructArguments($postValues) {
    return [
        'from' => generateDate($postValues['from']),
        'to' => generateDate($postValues['to']),
        'branches' => explode(' ', $postValues['branches']),
    ];
}

function generateDate($date) {
    $date = DateTime::createFromFormat('Y-m-d', $date);

    return $date->format('d-m-Y');
}

?>
<htmk>
    <head>
        <title>Kimeo: the GitHub report generator</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="col-10">
                    <h1>GitHub report generator</h1>
                    <form action="index.php" method="POST">
                        <div class="mb-3">
                            <label for="from">From (i.e: 2017-07-31)</label>
                            <input type="date" name="from" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="to">To From (i.e: 2017-07-31)</label>
                            <input type="date" name="to" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="branches">Branches names, separated by a space</label>
                            <input type="text" name="branches" class="form-control">
                        </div>
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">Generate Report</button>
                        </div>
                        </form>

                    <?php
                        if (isset($success)) {
                            echo $success;
                        }
                    ?>
                </div>
            </div>
        </div>
        <script type="text/javascript" src="https://cdn.rawgit.com/showdownjs/showdown/1.6.0/dist/showdown.min.js"></script>
        <script type="text/javascript">
            const converter = new showdown.Converter(),
                text      = document.querySelector('#text').innerHTML,
                html      = converter.makeHtml(text)
            ;
            const display   = document.querySelector('#display');
            display.innerHTML = html;
        </script>
    </body>
</htmk>
