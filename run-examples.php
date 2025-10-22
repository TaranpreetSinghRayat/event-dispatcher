#!/usr/bin/env php
<?php

/**
 * Quick Start Script for Event Dispatcher Examples
 * 
 * This script helps you run the example files easily.
 */

$examples = [
    '1' => [
        'name' => 'Basic Usage',
        'file' => 'examples/core-php/basic-usage.php',
        'description' => 'Demonstrates basic event dispatching, priorities, and propagation control'
    ],
    '2' => [
        'name' => 'Listener Classes',
        'file' => 'examples/core-php/listener-classes.php',
        'description' => 'Shows how to use class-based listeners'
    ],
    '3' => [
        'name' => 'Event Subscribers',
        'file' => 'examples/core-php/subscriber-example.php',
        'description' => 'Demonstrates event subscribers for grouping related listeners'
    ],
];

echo "\n";
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║         Event Dispatcher - Example Runner                     ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n";
echo "\n";

echo "Available Examples:\n\n";

foreach ($examples as $key => $example) {
    echo "  [{$key}] {$example['name']}\n";
    echo "      {$example['description']}\n\n";
}

echo "  [a] Run all examples\n";
echo "  [q] Quit\n\n";

echo "Enter your choice: ";
$choice = trim(fgets(STDIN));

echo "\n";

if ($choice === 'q' || $choice === 'Q') {
    echo "Goodbye!\n\n";
    exit(0);
}

if ($choice === 'a' || $choice === 'A') {
    foreach ($examples as $key => $example) {
        runExample($example);
    }
} elseif (isset($examples[$choice])) {
    runExample($examples[$choice]);
} else {
    echo "Invalid choice!\n\n";
    exit(1);
}

echo "\n";
echo "Done! Check the examples directory for more information.\n\n";

function runExample($example)
{
    echo "╔════════════════════════════════════════════════════════════════╗\n";
    echo "║ Running: {$example['name']}\n";
    echo "╚════════════════════════════════════════════════════════════════╝\n\n";
    
    if (!file_exists($example['file'])) {
        echo "Error: Example file not found: {$example['file']}\n\n";
        return;
    }
    
    require $example['file'];
    
    echo "\n";
    echo str_repeat("─", 64) . "\n\n";
}

