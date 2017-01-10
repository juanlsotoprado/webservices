<?php 

    include('qrlib.php'); 
    // outputs image directly into browser, as PNG stream 
    QRcode::png($_GET['t']);
