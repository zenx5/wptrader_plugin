<?php


?>
<v-tab-item >
    <v-card flat>
        <v-form>
            <v-container>
                <v-row>
                    <v-col>
                        <v-data-table
                            :headers="headerSetting"
                            :items="rates"
                        >
                            <template #item.color="{item, index}">
                                <v-text-field
                                    type="color"
                                    v-if="index==0"
                                    v-model="newRate.color">
                                </v-text-field>
                                <span 
                                    v-else
                                    :style="'border-radius: 50%; display: block; background-color:'+item.color+'; width: 25px; height: 25px; padding:1px;'"
                                ></span>
                            </template>
                            <template #item.rate="{item, index}">
                                <v-text-field 
                                    type="number" 
                                    v-if="index==0" 
                                    v-model="newRate.rate"
                                    min="0"></v-text-field>
                                <span v-else>{{item.rate}}</span>
                            </template>
                            <template #item.investmin="{item, index}">
                                <v-text-field 
                                    type="number" 
                                    v-if="index==0" 
                                    v-model="newRate.investmin"
                                    min="0"></v-text-field>
                                <span v-else>{{item.investmin}}</span>
                            </template>
                            <template #item.investmax="{item, index}">
                                <v-text-field 
                                    v-if="index==0"
                                    type="number" 
                                    v-model="newRate.investmax"
                                    min="0"></v-text-field>
                                <span v-else>{{item.investmax}}</span>
                            </template>
                            <template #item.action="{item, index}">
                                <v-icon v-if="index==0" @click="createRate">mdi-content-save</v-icon>
                                <v-icon v-else @click="deleteRate(index)">mdi-delete</v-icon>
                            </template>
                        </v-data-table>
                    </v-col>
                </v-row>
            </v-container>
        </v-form>
        
    </v-card>
</v-tab-item>