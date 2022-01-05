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
                    <v-col cols="5">
                        <h3>Configuraciones generales</h3>
                    </v-col>
                </v-row>
                <v-row>
                    <v-col cols="5">
                        <v-row>
                            <v-col>
                                <v-text-field 
                                    type="number"
                                    min="0"
                                    label="Retiro Mínimo"
                                    v-model="settings.rmin"
                                    append-icon="mdi-currency-usd"
                                ></v-text-field>
                            </v-col>
                            <v-col> 
                                <v-btn
                                    style="color: black; margin-top: 10px"
                                    @click="reset"
                                > Reset </v-btn>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col>
                                <v-text-field 
                                    type="number"
                                    min="0"
                                    label="Plazo para Cobro"
                                    v-model="settings.tiempoCobro"
                                    suffix="dias"
                                ></v-text-field>
                            </v-col>
                            <v-col> 
                                <v-btn
                                    style="color: black; margin-top: 10px"
                                    @click="save('wpt_settings',0)"
                                > Enviar </v-btn>
                            </v-col>
                        </v-row>
                    </v-col>
                    <v-col>
                        <v-data-table
                            :headers="headerAction"
                            :items="actions"
                        >
                            <template #item.precio="{item, index}">
                                <v-text-field 
                                    type="number" 
                                    v-if="index==0"
                                    min="0"
                                    step="0.01"
                                    v-model="newActions.precio"
                                    append-icon="mdi-currency-usd"
                                ></v-text-field>
                                <span v-else>{{item.precio}}<v-icon>mdi-currency-usd</v-icon></span>
                            </template>
                            <template #item.foot="{item, index}">
                                <v-text-field 
                                    type="number" 
                                    v-if="index==0" 
                                    min="0" 
                                    v-model="newActions.foot"
                                ></v-text-field>
                                <span v-else>{{item.foot}}</span>
                            </template>
                            <template #item.head="{item, index}">
                                <v-text-field 
                                    v-if="index==0"
                                    type="number"
                                    min="0"
                                    v-model="newActions.head"
                                ></v-text-field>
                                <span v-else>{{item.head}}</span>
                            </template>
                        </v-data-table>
                    </v-col>
                </v-row>
                <v-row>
                    <v-col>
                        <v-row cols="5">
                            <h3>Paises</h3>
                        </v-row>
                        <v-row>
                            <v-data-table
                                :headers="headerCountrie"
                                :items="countries"
                                class="elevation-1"
                            >
                                <template #item.enable="{item}">
                                    <v-checkbox
                                        v-model="item.enable"
                                        class="pa-3"
                                        @click="localStorage.setItem('wpt_countries', JSON.stringify(countries))"
                                    ></v-checkbox>
                                </template>
                            </v-data-table>
                        </v-row>
                    </v-col>
                    <v-col><v-col>
                </v-row>
            </v-container>
        </v-form>
        
    </v-card>
</v-tab-item>