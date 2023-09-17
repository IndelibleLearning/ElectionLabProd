<link rel="stylesheet" href="css/game-tutorials.css">

<div id="game-tutorials" class="game-tutorials hidden">
    <div class="game-tutorials-container elo-action-screen">
        <div id="close-tutorials" class="close-tutorials">X</div>

        <!-- Tab Links -->
        <div class="tab-links">
            <button class="tab-link current-tab elo-button" data-tab-content="Overview">Overview</button>
            <button class="tab-link elo-button" data-tab-content="Deployments">Deployments</button>
            <button class="tab-link elo-button" data-tab-content="Dice">Dice</button>
        </div>

        <!-- Tab Content -->
        <div id="Overview" class="tabcontent">
            <?php
            include __DIR__ . '/overview_tutorial.php';
            ?>        </div>
        <div id="Deployments" class="tabcontent hidden">
            <?php
            include __DIR__ . '/deployments_tutorial.php';
            ?>        </div>
        <div id="Dice" class="tabcontent hidden">
            <?php
            include __DIR__ . '/dice_tutorial.php';
            ?>
        </div>

    </div>
</div>
