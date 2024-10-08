<?php
include_once('Database.php');


$sql = "SELECT DATE(date) as log_date, COUNT(*) as log_count FROM logs GROUP BY log_date";

try {
    
    $stmt = $dbh->prepare($sql);
    $stmt->execute();

  
    $logs_par_jour = [];
    while ($row = $stmt->fetch()) {
        $logs_par_jour[$row['log_date']] = $row['log_count'];
    }

} catch (PDOException $e) {
   
    error_log('Erreur SQL : ' . $e->getMessage());
    die('Erreur lors de la récupération des données. Veuillez réessayer plus tard.');
}


$dates = json_encode(array_keys($logs_par_jour));
$log_counts = json_encode(array_values($logs_par_jour));
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courbe des logs</title>
    <style>
        canvas {
            border: 1px solid #000;
        }
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            flex-direction: column;
        }
        .button-link {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #ff6f61;
            color: black;
            text-decoration: none;
            border-radius: 5px;
            font-family: Arial, sans-serif;
            font-size: 14px;
        }
        .button-link:hover {
            background-color: #ffa07a;
        }
    </style>
</head>
<body>
    <canvas id="logsChart" width="600" height="400"></canvas>
    <script>
       
        var dates = <?php echo $dates; ?>;
        var logCounts = <?php echo $log_counts; ?>;

       
        var canvas = document.getElementById('logsChart');
        var ctx = canvas.getContext('2d');

     
        var width = canvas.width;
        var height = canvas.height;
        var padding = 40; 

       
        var maxLogCount = Math.max(...logCounts);
        var xStep = (width - 2 * padding) / (dates.length - 1);
        var yRatio = (height - 2 * padding) / maxLogCount;

       
        ctx.beginPath();
        ctx.moveTo(padding, padding);
        ctx.lineTo(padding, height - padding);
        ctx.lineTo(width - padding, height - padding);
        ctx.stroke();

       
        ctx.fillStyle = 'black';
        ctx.font = '12px Arial';
        var ySteps = 5; 
        for (var i = 0; i <= ySteps; i++) {
            var yValue = Math.round((maxLogCount / ySteps) * i);
            var yPosition = height - padding - (yValue * yRatio);
            ctx.fillText(yValue, padding - 30, yPosition + 5);
           
            ctx.beginPath();
            ctx.moveTo(padding - 5, yPosition);
            ctx.lineTo(padding, yPosition);
            ctx.stroke();
        }

     
        ctx.beginPath();
        ctx.moveTo(padding, height - padding - logCounts[0] * yRatio);
        for (var i = 1; i < dates.length; i++) {
            var x = padding + i * xStep;
            var y = height - padding - logCounts[i] * yRatio;
            ctx.lineTo(x, y);
        }
        ctx.strokeStyle = 'blue';
        ctx.stroke();

       
        for (var i = 0; i < dates.length; i++) {
            var x = padding + i * xStep;
            var y = height - padding - logCounts[i] * yRatio;
            ctx.beginPath();
            ctx.arc(x, y, 3, 0, 2 * Math.PI);
            ctx.fillStyle = 'red';
            ctx.fill();
        }

       
        for (var i = 0; i < dates.length; i++) {
            var x = padding + i * xStep;
            ctx.fillText(dates[i], x - 20, height - padding + 20);
        }
    </script>
    <div style="margin-top: 20px;">
        <a href="/index.php" class="button-link">Retour aux Back</a>
    </div>
</body>
</html>
