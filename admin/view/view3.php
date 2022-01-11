<?php


?>
<v-tab-item >
    <v-card flat>
        <v-card-title > 
            {{ temp.nombre }} {{ temp.apellido }}
        </v-card-title>
        <v-card-subtitle>  {{ temp.cedula }} </v-card-subtitle>

        <v-card-text>
            <v-row>
                <v-col cols="3">
                    <b>Correo</b>
                </v-col>
                <v-col cols="3">
                    <i> {{ temp.correo }} </i>
                </v-col>
                <v-col cols="6">
                    <count-down
                        :data-day="nextPay(temp.id)"
                        data-display="d:h:m"
                    >
                    </count-down>
                </v-col>
            </v-row>
            <v-row>
                <v-col cols="3">
                    <b>Pais</b>
                </v-col>
                <v-col cols="3">
                    <i> {{ temp.pais }} </i>
                </v-col>
                <v-col cols="6">
                    
                </v-col>
            </v-row>
            <v-row>
                <v-col cols="6">
                    <b>Codigo Postal</b>
                </v-col>
                <v-col cols="6">
                    <i> {{ temp.postalcode }} </i>
                </v-col>
            </v-row>
            <v-row>
                <v-col cols="6">
                    <b>Telefono</b>
                </v-col>
                <v-col cols="6">
                    <i> {{ temp.telefono }} </i>
                </v-col>
            </v-row>
            <v-divider></v-divider>
            <v-row>
                <v-col>
                    <h3>Nueva Inversion</h3>
                </v-col>
                <v-col cols="2">
                    
                </v-col>
                <v-col>
                    <h3>Acciones</h3>
                </v-col>
            </v-row>
            <v-row>
                <v-col cols="4">
                    <v-form>                    
                        <v-text-field
                            type="date"
                            label="Fecha"
                            v-model="newInvestment.fecha">
                        </v-text-field>
                        <v-text-field
                            type="number"
                            label="Monto"
                            min="0"
                            v-model="newInvestment.monto"
                            append-icon="mdi-currency-usd">
                        </v-text-field>
                        <v-btn @click="save('wpt_investments', -1)">
                            <v-icon>mdi-content-save</v-icon>Agregar
                        </v-btn>
                    </v-form>
                </v-col>     
                <v-col cols="2">
                    
                </v-col>
                <v-col>
                    <v-form>
                        <span>
                            Posee <b>{{ temp.actions | totalActions('cantidad') }}</b> con un valor de <b>{{ temp.actions | totalActions('valor') }}$</b>
                        </span>
                        <v-text-field
                            type="number"
                            label="Numero de Acciones"
                            v-model="currentActions"
                            min="0"
                            :max="settings.actionMax - $options.filters.totalActions(temp.actions, 'cantidad')">
                        </v-text-field>
                        <v-btn @click="setAction" :disabled="validateAction">
                            <v-icon>mdi-content-save</v-icon>Agregar
                        </v-btn>
                    </v-form>
                </v-col>
            </v-row>
            <v-divider></v-divider>
            <v-row>
                <v-col cols="12">
                    <v-data-table
                        :headers="headerInvestment"
                        :items="investments | forKey('usuario',temp.id) | fechaCobro(settings.tiempoCobro)"
                    >
                    <template #item.fecha="{item}" >
                        <span :style="item.released?'text-decoration: line-through;':''">
                            {{ item.fecha | date }}
                        </span>
                    </template>
                    <template #item.fechacobro="{item}">
                        <span :style="item.released?'text-decoration: line-through;':''">
                            {{ item.fechacobro | date }}
                        </span>
                    </template>
                    <template #item.monto="{item}">
                        <span :style="item.released?'text-decoration: line-through;':''">
                            {{ item.monto }} $
                        </span>
                    </template>
                    <template #item.cobro="{item}">
                        <span v-if="item.cobro>0">
                            {{ item.cobro }} dias
                        </span>
                        <span v-else>
                            <v-chip color="#0f0" v-if="item.released">Cobrado</v-chip>
                            <v-chip color="#0f0" v-else>Por Cobrar</v-chip>
                        </span>
                    </template>
                    <template #item.generado="{item}">
                        <span>
                            {{porCobrar( settings.tiempoCobro-item.cobro, item.monto, 0 )}}
                        </span>
                    </template>
                    <template #item.action="{item}">
                        <v-progress-circular
                            :rotate="-90"
                            :size="30"
                            :width="4"
                            :value="100-100*item.cobro/settings.tiempoCobro">
                            <v-icon 
                                @click="cobrar"
                                :disabled="item.cobro>0"
                                v-if="!item.released">mdi-currency-usd</v-icon>
                        </v-progress-circular>
                        <v-progress-circular
                            :rotate="-90"
                            :size="30"
                            :width="4"
                            :value="diasLeft(1,16)">
                            <v-icon 
                                @click=""
                                v-if="!item.released">mdi-currency-usd</v-icon>    
                        </v-progress-circular>                        
                        <v-icon 
                            @click="del('wpt_investments', item.id)"
                            v-if="!item.released">mdi-delete</v-icon>
                        
                    </template>
                    </v-data-table>
                </v-col>
            </v-row>
        </v-card-text>
    
        <v-card-actions>
            <v-row>
                <v-col cols="6">
                    <v-btn @click="view">Volver</v-btn>
                </v-col>
            </v-row>
        </v-card-actions>
    </v-card>
</v-tab-item>