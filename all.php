<html>
 <head>
 <Title>Registration Form</Title>
 <style type="text/css">
 	body { background-color: #fff; border-top: solid 10px #000;
 	    color: #333; font-size: .85em; margin: 20; padding: 20;
 	    font-family: "Segoe UI", Verdana, Helvetica, Sans-Serif;
 	}
 	h1, h2, h3,{ color: #000; margin-bottom: 0; padding-bottom: 0; }
 	h1 { font-size: 2em; }
 	h2 { font-size: 1.75em; }
 	h3 { font-size: 1.2em; }
 	table { margin-top: 0.75em; }
 	th { font-size: 1.2em; text-align: left; border: none; padding-left: 0; }
 	td { padding: 0.25em 2em 0.25em 0em; border: 0 none; }
 </style>
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
 </head>
 <body>
 <script type="text/javascript">
    function processImage() {
        // **********************************************
        // *** Update or verify the following values. ***
        // **********************************************
 
        // Replace <Subscription Key> with your valid subscription key.
        var subscriptionKey = "a283b9248a4340ce9a3bde30476946d9";
 
        // You must use the same Azure region in your REST API method as you used to
        // get your subscription keys. For example, if you got your subscription keys
        // from the West US region, replace "westcentralus" in the URL
        // below with "westus".
        //
        // Free trial subscription keys are generated in the "westus" region.
        // If you use a free trial subscription key, you shouldn't need to change
        // this region.
        //var uriBase = "https://anonymousliemVision.cognitiveservices.azure.com/vision/v2.0/analyze";
         var uriBase = "https://anonymousliemVision.cognitiveservices.azure.com/vision/v2.0/analyze"
        // Request parameters.
        var params = {
            "visualFeatures": "Categories,Description,Tags",
            "details": "",
            "language": "en",
        };
 
        // Display the image.
        var sourceImageUrl = document.getElementById("inputImage").value;
        document.querySelector("#sourceImage").src = sourceImageUrl;
 
        // Make the REST API call.
        $.ajax({
            url: uriBase + "?" + $.param(params),
 
            // Request headers.
            beforeSend: function(xhrObj){
                xhrObj.setRequestHeader("Content-Type","application/json");
                xhrObj.setRequestHeader(
                    "Ocp-Apim-Subscription-Key", subscriptionKey);
            },
 
            type: "POST",
 
            // Request body.
            data: '{"url": ' + '"' + sourceImageUrl + '"}',
        })
 
        .done(function(data) {
           
            // Show formatted JSON on webpage.
           var  obj =  $("#responseTextArea2").val(JSON.stringify(data, null, 2));
           var myJSON = JSON.stringify(data, null, 2);

          var parsing = JSON.parse(myJSON);

           document.getElementById("demo").innerHTML = parsing.description.captions[0].text;
        })
 
        .fail(function(jqXHR, textStatus, errorThrown) {
            // Display error message.
            var errorString = (errorThrown === "") ? "Error. " :
                errorThrown + " (" + jqXHR.status + "): ";
            errorString += (jqXHR.responseText === "") ? "" :
                jQuery.parseJSON(jqXHR.responseText).message;
            alert(errorString);
        });
    };
</script>
 <form action="all.php" method="post" enctype="multipart/form-data">
        Pilih file: <input type="file" name="berkas" />
        <br>
        <input type="submit" name="upload" value="upload" />
    </form> 

 <?php
 require_once 'vendor/autoload.php';
 require_once "./random_string.php";
 
 use MicrosoftAzure\Storage\Blob\BlobRestProxy;
 use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
 use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
 use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
 use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;
  if (isset($_POST['upload'])) {
        // ambil data file
        $namaFile = $_FILES['berkas']['name'];
        $namaSementara = $_FILES['berkas']['tmp_name'];
        $fileToUpload = $_FILES['berkas']['name'];
        // tentukan lokasi file akan dipindahkan
        $dirUpload = "terupload/";
       
      //  $containerName = "testslurs".generateRandomString();
      $containerName = "testslurs".generateRandomString();
// pindahkan file
        $terupload = move_uploaded_file($namaSementara, $namaFile);
  
if ($terupload) {

    $link = "https://anonymousliem.blob.core.windows.net/";
    $slash = "/";
    echo '<img src="'.$link.$containerName.$slash.$fileToUpload.'">';
    echo"<br>";
    echo "These are the blobs present in the container: "."https://anonymousliem.blob.core.windows.net/".$containerName."/".$namaFile;
    echo "<br>";
    echo "Link: <a href='".$link.$containerName.$slash.$fileToUpload."'>".$namaFile."</a>";
    echo"<br>";
    echo "<br>";
    echo "<input type='text' name='inputImage' id='inputImage' readonly value='$link$containerName$slash$fileToUpload'>";
    echo "<button onclick='processImage()'>Analyze image</button>";
    echo "
    <div id='wrapper' style='width:1020px; display:table;'>
        <div id='jsonOutput' style='width:600px; display:table-cell;'>
            Response:
            <br><br>
            <textarea id='responseTextArea2' class='UIInput'
                      style='width:580px; height:400px;'></textarea>
        </div>
        <div id='imageDiv' style='width:420px; display:table-cell;'>
            Source image:
            <br><br>
            <img id='sourceImage' width='400' />
            <p id='demo'></p>
        </div>
        
    </div>
 
    ";
} else {
    echo "Upload Gagal!";
} 

$connectionString = "DefaultEndpointsProtocol=https;AccountName=anonymousliem;AccountKey=cCoHpsQemjlOWzGszVkAMlbxcrUp2As9TjoQRFhheIn7LM1pzGSeYSpC2wgKR84R4OSaaEJjJrZxg2CAIBgEQg==;EndpointSuffix=core.windows.net";
// Create blob client.
$blobClient = BlobRestProxy::createBlobService($connectionString);

//$fileToUpload = "logo.png";
if (!isset($_GET["Cleanup"])) {
    // Create container options object.
    $createContainerOptions = new CreateContainerOptions();

    // Set public access policy. Possible values are
    // PublicAccessType::CONTAINER_AND_BLOBS and PublicAccessType::BLOBS_ONLY.
    // CONTAINER_AND_BLOBS:
    // Specifies full public read access for container and blob data.
    // proxys can enumerate blobs within the container via anonymous
    // request, but cannot enumerate containers within the storage account.
    //
    // BLOBS_ONLY:
    // Specifies public read access for blobs. Blob data within this
    // container can be read via anonymous request, but container data is not
    // available. proxys cannot enumerate blobs within the container via
    // anonymous request.
    // If this value is not specified in the request, container data is
    // private to the account owner.
    $createContainerOptions->setPublicAccess(PublicAccessType::CONTAINER_AND_BLOBS);

    // Set container metadata.
    $createContainerOptions->addMetaData("key1", "value1");
    $createContainerOptions->addMetaData("key2", "value2");

      //$containerName = "blockblobs".generateRandomString();
    try {
        // Create container.
        $blobClient->createContainer($containerName, $createContainerOptions);

        // Getting local file so that we can upload it to Azure
        $myfile = fopen($fileToUpload, "r") or die("Unable to open file!");
        fclose($myfile);
        
        # Upload file as a block blob
        echo "<br>";
        //echo "Uploading BlockBlob: ".PHP_EOL;
       // echo $fileToUpload;
        echo "<br />";
        
        $content = fopen($fileToUpload, "r");

        //Upload blob
        $blobClient->createBlockBlob($containerName, $fileToUpload, $content);

        // List blobs.
        $listBlobsOptions = new ListBlobsOptions();
        $listBlobsOptions->setPrefix("HelloWorld");

      
      

        do{
            $result = $blobClient->listBlobs($containerName, $listBlobsOptions);
            foreach ($result->getBlobs() as $blob)
            {
                echo $blob->getName().": ".$blob->getUrl()."<br />";
            }
        
            $listBlobsOptions->setContinuationToken($result->getContinuationToken());
        } while($result->getContinuationToken());
        echo "<br />";

        // Get blob.
       /* echo "This is the content of the blob uploaded: ";
        $blob = $blobClient->getBlob($containerName, $fileToUpload);
        fpassthru($blob->getContentStream());
        echo "<br />"; */
    }
    catch(ServiceException $e){
        // Handle exception based on error codes and messages.
        // Error codes and messages are here:
        // http://msdn.microsoft.com/library/azure/dd179439.aspx
        $code = $e->getCode();
        $error_message = $e->getMessage();
        echo $code.": ".$error_message."<br />";
    }
    catch(InvalidArgumentTypeException $e){
        // Handle exception based on error codes and messages.
        // Error codes and messages are here:
        // http://msdn.microsoft.com/library/azure/dd179439.aspx
        $code = $e->getCode();
        $error_message = $e->getMessage();
        echo $code.": ".$error_message."<br />";
    }
} 
else 
{

    try{
        // Delete container.
        echo "Deleting Container".PHP_EOL;
        echo $_GET["containerName"].PHP_EOL;
        echo "<br />";
        $blobClient->deleteContainer($_GET["containerName"]);
    }
    catch(ServiceException $e){
        // Handle exception based on error codes and messages.
        // Error codes and messages are here:
        // http://msdn.microsoft.com/library/azure/dd179439.aspx
        $code = $e->getCode();
        $error_message = $e->getMessage();
        echo $code.": ".$error_message."<br />";
    }
}

}else{
    echo "Belum ada gambar";
}

?>

<!--
<form method="post" action="phpQS.php?Cleanup&containerName=<?php echo $containerName; ?>">
    <button type="submit">Press to clean up all resources created by this sample</button>
</form> -->

<!-- <br>
Image to analyze:
<input type="text" name="inputImage" id="inputImage"
    value="http://upload.wikimedia.org/wikipedia/commons/3/3c/Shaki_waterfall.jpg" /> -->


 </body>
 </html>