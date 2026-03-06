<?php
declare(strict_types=1);

return [
  "db" => [
    "host" => "127.0.0.1",
    "name" => "cse135_analytics",
    "user" => "cse135",
    "pass" => "StrongPasswordHere", // use env later if needed
  ],

  // single grader login (no signup required)
  "auth" => [
    "username" => "grader",
    "password" => "UCSD1234"
  ],
];