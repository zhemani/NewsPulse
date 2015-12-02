

<html xmlns="http://www.w3.org/1999/xhtml">
    
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">
    
		<link rel="stylesheet" href="style/normalize.css"/>
		<link rel="stylesheet" href="style/main.css"/>
    
    

<title>Welcome - Movie Database</title>
</head>
    <body>
        <header>
            <div id="headwrap">
            
                <p>Input tags to search news.</p>
                
                
                <form action="test.php" method="post" id="addmovieform">

                    <div class="input_movie">
                        <textarea name="tags" cols="30" rows="20"><?php
                            if (isset($_POST['tags'])) {
                                // Escape any html characters
                                echo trim($_POST['tags']);
                            }

                        ?></textarea>
                    </div>
                    
                    <div id="searchsubmit">
                        <input class="btn1" type="submit" name="search" value="Search"/>
                    </div>
                
                </form>
            
            </div>
            
            
        </header>
        <section id="first_section">
                <p>Map goes here</p>
            <?php
    
                if (isset($_POST['tags'])) {
                // Escape any html characters
                    echo htmlentities($_POST['tags']);
                    $tags = htmlentities($_POST['tags']);
                }
                    
                
                $tags_array = explode(' ', $tags);
                
                var_dump($tags_array);
        

            ?>
        
        </section>
       

        
    </body>
</html>  