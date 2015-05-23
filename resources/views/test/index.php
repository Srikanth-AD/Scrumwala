<!DOCTYPE html>
<html lang="en" ng-app="app">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>reAch</title>
</head>
<body>
<div ng-controller="IssuesController">

    <input type="text" ng-model="search">
    <ul>
        <li ng-repeat="issue in issues | filter:search">
            <input type="checkbox" ng-model="issue.completed">
            {{ issue.title }}
        </li>
    </ul>
    <form ng-submit="addIssue()">
        <input type="text" placeholder="add new issue" ng-model="newIssueText">
        <button type="submit">add issue</button>
    </form>
</div>

<!-- Scripts -->
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/angularjs/1.3.14/angular.min.js"></script>
<script src="/js/main.js"></script>
</body>
</html>
