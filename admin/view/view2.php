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
                                    :style="'border-radius: 50%; display: block; background-color:'+item.color+'; width: 25px; height: 25px; padding:1px;border:1px solid black;'"
                                ></span>
                            </template>
                            <template #item.rate="{item, index}">
                                <v-text-field 
                                    type="number" 
                                    v-if="index==0" 
                                    v-model="newRate.rate"
                                    min="0"
                                    max="100"
                                    step="0.01"
                                    append-icon="mdi-percent"
                                ></v-text-field>
                                <span v-else>{{item.rate}}<v-icon>mdi-percent</v-icon></span>
                            </template>
                            <template #item.investmin="{item, index}">
                                <v-text-field 
                                    type="number" 
                                    v-if="index==0" 
                                    v-model="newRate.investmin"
                                    min="0"
                                    append-icon="mdi-currency-usd"
                                ></v-text-field>
                                <span v-else>{{item.investmin}}<v-icon>mdi-currency-usd</v-icon></span>
                            </template>
                            <template #item.investmax="{item, index}">
                                <v-text-field 
                                    v-if="index==0"
                                    type="number"
                                    v-model="newRate.investmax"
                                    min="0"
                                    append-icon="mdi-currency-usd"
                                ></v-text-field>
                                <span v-else>{{item.investmax}}<v-icon>mdi-currency-usd</v-icon></span>
                            </template>
                            <template #item.action="{item, index}">
                                <v-icon v-if="index==0" @click="save('wpt_rates',-1)">mdi-content-save</v-icon>
                                <v-icon v-else @click="del('wpt_rates',item.id)">mdi-delete</v-icon>
                            </template>
                        </v-data-table>
                    </v-col>
                </v-row>
                    
                <v-divider/></v-divider>
                
                <v-row>
                    <v-col>
                        <h3>Configuraciones generales</h3>
                    </v-col>
                </v-row>
                <v-row>
                    <v-col cols="5">
                        <v-text-field 
                            type="number"
                            min="0"
                            label="Retiro Mínimo"
                            v-model="rmin"
                            append-icon="mdi-currency-usd"
                        ></v-text-field>
                    </v-col>
                    <v-col cols="5">
                        <v-text-field 
                            type="number"
                            min="0"
                            label="Plazo para Cobro"
                            v-model="tiempoCobro"
                            suffix="dias"
                        ></v-text-field>
                    </v-col>
                    <v-col> 
                        <v-btn
                            style="color: black; margin-top: 10px"
                        > Enviar </v-btn>
                    </v-cols>
                </v-row>
            </v-container>
        </v-form>
        
    </v-card>
</v-tab-item>