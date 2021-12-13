<?php

   
?>

<style>
    #wp-trader-app .action {
        display: flex;
        flex-direction:row;
        justify-content: center;
    }
    #wp-trader-app .action button {
        margin-left: 20px;
        margin-right: 20px;
    }
    #wp-trader-app .v-input input:focus, .v-input input:active{
        border: none !important;
        box-shadow: none !important;
    }

    #wp-trader-app .v-input--is-disabled input, #wp-trader-app .v-input--is-disabled .v-select__selection{
        color: rgba(0,0,0,70%) !important;
    }
</style>
<div class="trader-app-container">
    
    <div id="wp-trader-app" data-app>
        <v-card>
            <v-toolbar>
                <v-tabs v-model="tab">
                    <v-tab v-for="tab in tabs" v-show="detailsMode(tab)"> {{tab}} </v-tab>
                </v-tabs>
            </v-toolbar>
            <v-tabs-items v-model="tab">
                <?php
                    include 'view1.php';
                    include 'view2.php';
                    include 'view3.php';
                ?>
            </v-tabs-items>
        </v-card>
    </div>
</div>

