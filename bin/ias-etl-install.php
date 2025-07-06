<?php
$appRoutingFile = __DIR__ . '/../../config/routes/ias_etl.yaml';
$import = "ias_etl:\n    resource: '../vendor/ias/ias-etl/config/routes/ias_etl.yaml'\n    type: yaml\n";

if (!file_exists($appRoutingFile)) {
    file_put_contents($appRoutingFile, $import);
    echo "✔️  ias_etl route imported to config/routes/ias_etl.yaml\n";
} else {
    echo "ℹ️  config/routes/ias_etl.yaml already exists.\n";
}
