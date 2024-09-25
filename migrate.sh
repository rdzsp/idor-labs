#!/bin/bash
cd bank-root && php create_database.php
cd ../blog && php create_database.php
cd ../ktp-uploader && php create_database.php