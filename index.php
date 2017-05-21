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

use Dotenv\Dotenv;
use Kimeo\GitHubGenerator;
use Symfony\Component\Debug\Debug;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;

Debug::enable();

$dotenv = new Dotenv(__DIR__);
$dotenv->load();

if ('POST' === $_SERVER['REQUEST_METHOD']) {
    $request = Request::createFromGlobals();
    $postValues = $request->request->all();
    $args = constructArguments($postValues);

    $user = getenv('GITHUB_LOGIN');
    $password = getenv('GITHUB_PASSWORD');
    $owner = getenv('GITHUB_OWNER');
    $project = getenv('GITHUB_REPOSITORY');

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
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="col-md-10">
                    <h1>GitHub report generator</h1>
                    <form action="index.php" method="POST">
                        <div class="form-group">
                            <label for="from">From (i.e: 2017-07-31)</label>
                            <input type="date" name="from" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="to">To From (i.e: 2017-07-31)</label>
                            <input type="date" name="to" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="branches">Branches names, separated by a space</label>
                            <input type="text" name="branches" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-default">Generate Report</button>
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
            var converter = new showdown.Converter(),
                text      = document.querySelector('#text').innerHTML,
                html      = converter.makeHtml(text)
            ;
            var display   = document.querySelector('#display');
            display.innerHTML = html;
        </script>
    </body>
</htmk>
