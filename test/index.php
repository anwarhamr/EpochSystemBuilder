<html>
<head>
  <title>Epoch System</title>
  <link href="../css/style.css" rel="stylesheet" type="text/css">
</head>
<body>
<h3><a href="example1.php">Example #1: All enabled</a></h3>
Because of the high probability of combinations that have no result AND the ability to add a custom message for each of those combinations -- this proof of concept would probably be best used as in distributor / internal sales [training] tool to explain things and redirect a customer to better solution.</p>
<h3><a href="example2.php">Example #2: Sequential</a></h3>
Because every combination will always have a successful result and everything else is hidden -- this proof of concept would be better customer facing to avoid frustration/discouragement.</p>
<h3>Database</h3>
Both proof of concepts use the exact same database.
<h3>Source Code</h3>
<a href="https://github.com/jonfen/EpochSystemBuilder">https://github.com/jonfen/EpochSystemBuilder</a>
<br />For security, place <i>config.ini</i> outsite of browseable files and change the path references in each example accordingly.
<pre>$config = parse_ini_file('../../config.ini');</pre>
<br /><a href="gains.php">Generate Gains SQL</a> was used to populate <a href="create_database.sql">create_database.sql</a>.
</body>
</html>
