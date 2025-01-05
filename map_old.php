<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Temperature Boxes</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      display: flex;
      flex-direction: column;
      align-items: center;
      position: relative;
    }

    .image-container {
      position: relative;
    }

    .background-image {
      display: block;
      max-width: 100%;
      height: auto;
    }

    .temp-box {
      position: absolute;
      background-color: black;
      color: white;
      padding: 5px 10px;
      border-radius: 5px;
      font-size: 20px;
      font-family: 'sans-serif'
      text-align: center; /* Centers text inside the box */
      transform: translate(-50%, -50%); /* Centers the boxes relative to their coordinates */
    }

    /* Adjust positions as percentages relative to the image */
    .box_LR { top: 25%;
        left: 24%; } /* Living Room */
    .box_BATH { top: 11%;
        left: 67%; } /* Bathroom */
    .box_BRAR { top: 59%;
        left: 59%; } /* Bedroom AR */
    .box_BRYH { top: 81%;
        left: 63%; } /* Bedroom YH */
    .box_KIT { top: 39%;
        left: 58%; } /* Kitchen */
    .box_OUT { top: 61%;
        left: 25%; } /* Outside */
     </style>
</head>
<body>
  <div class="image-container">
    <img src="maps.png" alt="Background" class="background-image">
    <div class="temp-box box_LR">19.27 °C<br>45%</div>
    <div class="temp-box box_BATH">20.5 °C<br>50%</div>
    <div class="temp-box box_BRAR">19.4 °C<br>55%</div>
    <div class="temp-box box_BRYH">17.76 °C<br>60%</div>
    <div class="temp-box box_KIT">18.17 °C<br>48%</div>
    <div class="temp-box box_OUT">20.43 °C<br>35%</div>  </div>
</body>
</html>
