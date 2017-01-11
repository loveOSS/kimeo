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
use Symfony\Component\Debug\Debug;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Process\ProcessBuilder;
use Symfony\Component\Process\Exception\ProcessFailedException;

Debug::enable();

if ('POST' === $_SERVER['REQUEST_METHOD']) {
    $request = Request::createFromGlobals();
    $postValues = $request->request->all();

    $builder = new ProcessBuilder();
    $builder->setPrefix('./core-weekly-generator');
    $builder->setArguments(constructArguments($postValues));

    $process = $builder->getProcess();
    try {
        $process->mustRun();
        $success = "<a href='weekly-report.md' download>Download here</a>"
            . "<div id='text' style='display:none;'>"
            . file_get_contents('weekly-report.md')
            . "</div>"
            . "<div id='display'></div>"
        ;
    } catch (ProcessFailedException $e) {
        echo $e->getMessage();
    }
}

function constructArguments($postValues) {
    $arguments = [
        $postValues['login'],
        $postValues['password'],
        generateDate($postValues['from']),
        generateDate($postValues['to'])
    ];

    $branches = explode(' ', $postValues['branches']);
    foreach ($branches as $branch) {
        $arguments[] = $branch;
    }

    return $arguments;
}

function generateDate($date) {
    $date = DateTime::createFromFormat('Y-m-d', $date);

    return $date->format('d-m-Y');
}

?>
<htmk>
    <head>
        <title>Core weekly generator</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="col-md-10">
                    <h1>Core weekly generator</h1>
                    <form action="index.php" method="POST">
                        <div class="form-group">
                            <label for="login">GitHub Login</label>
                            <input type="text" name="login" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="password">GitHub Password</label>
                            <input type="password" name="password" class="form-control">
                        </div>
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
                        <button type="submit" class="btn btn-default">Generate Weekly</button>
                    </form>

                    <?php
                        if (isset($success)) {
                            echo '<p> Generation done !!</p>';
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
