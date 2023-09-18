<link rel="stylesheet" href="css/game-postgame.css">

<div id="post-game-area" class="hidden elo_test_section post-game-area">
  <h1>Which was your strategy?</h1>
  <div id="strategies" class="strategies">
      <div id="strategy-container-A" class="post-game-strategy">
          <div class="strategy-letter">
            A
          </div>
          <div id="strategy-A" class="strategy-image">
             <?php include 'histograms/strategy_A_image.php' ?>
          </div>

        <input class="strategy-radio" type="radio" name="strategy" value="A">
      </div>
      <div id="strategy-container-B" class="post-game-strategy">
          <div class="strategy-letter">
            B
          </div>
          <div id="strategy-B" class="strategy-image">
           <?php include 'histograms/strategy_B_image.php' ?>
          </div>
          
          <input class="strategy-radio" type="radio" name="strategy" value="B">
      </div>
      <div id="strategy-container-C" class="post-game-strategy">
          <div class="strategy-letter">
            C
          </div>
           <div id="strategy-C" class="strategy-image">
         
           <?php include 'histograms/strategy_C_image.php' ?>
           </div>
           
         <input class="strategy-radio" type="radio" name="strategy" value="C">
      </div>
      <div id="strategy-container-D" class="post-game-strategy">
          <div class="strategy-letter">
            D
          </div>
          <div id="strategy-D" class="strategy-image">
           <?php include 'histograms/strategy_D_image.php' ?>
          </div>
          
          <input class="strategy-radio" type="radio" name="strategy" value="D">
      </div>
  </div>
  <div id="blank-strategy" class="strategy-image">
     <?php include 'histograms/blank_strategy_image.php' ?>
  </div>
  <button id="answer-question" class="elo-button">Select</button>
    <button id="postgame-back-to-lobby" class="hidden elo-button">Back to Lobby</button>
</div>