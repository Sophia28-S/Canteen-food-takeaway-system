<?php
// register_submit.php is no longer needed as a separate file —
// register.php now handles its own POST submission.
// This file redirects to register.php for safety.
header("Location: register.php");
exit;