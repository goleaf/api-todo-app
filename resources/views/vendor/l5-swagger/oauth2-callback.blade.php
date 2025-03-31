<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Swagger UI OAuth2 Callback</title>
</head>
<body>
<script>
    window.onload = function() {
        window.opener.swaggerUIRedirectOauth2(window.location);
        window.close();
    }
</script>
</body>
</html> 