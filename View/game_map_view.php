<link rel="stylesheet" href="css/game_map_view.css">

<div id="map-area" class="hidden map-section">
    <div id="turn_display" class="hidden turn-display">
    </div>
    <div class="map-background">
    </div>
    <div class="map-foreground">
        <div class="score-area">
          <image src="images/game_assets/Year_2020.png" class="map-year"></image>

          <div id="blue-scorecard" class="blue-scorecard scorecard">
              <div id="blue_player_name"  class="player-name"></div>
              <div id="blue_bar" class="bar blue">
                  <div class="bar-fill">
                      <div class="star">
                          <image src="images/game_assets/StarBlue.png" class="bar-star"/>
                        <div id="blue_score" class="score"></div>
                      </div>
                  </div>
              </div>
            </div>
            <div class="votes-container">
                <image src="images/game_assets/ElectoralVotes_270.png" class="map-votes"></image>
                
                <div id="game-label" class="game-label elo-action-screen color-text hidden">
                </div>
            </div>
            <div id="red-scorecard" class="red-scorecard scorecard">
              <div id="red_player_name" class="player-name"></div>
              <div id="red_bar" class="bar">
                  <div class="bar-fill">
                      <div class="star">
                          <image src="images/game_assets/StarRed.png" class="bar-star"/>
                            <div id="red_score" class="score"></div>
                      </div>
                  </div>
              </div>
          </div>
        </div>
        
        <div class="play-area">
	        <? include 'game_wait_view.php'; ?>
            <div class="map-image-container">
              <?php
                include 'maps/map_image_2024.php';
              ?> 
            </div>
            <div class="cards-area">
                
            </div>
        </div>
    </div>
    
    
    
</div>