<?php
$appRoutingFile = __DIR__ . '/../../config/routes/ias_etl.yaml';
if (file_exists($appRoutingFile)) {
    unlink($appRoutingFile);
    echo "🗑️  Removed config/routes/ias_etl.yaml\n";
} else {
    echo "ℹ️  No route file to clean.\n";
}
