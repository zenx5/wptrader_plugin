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
            </v-row>
            <v-form>
                <v-row>
                    <v-col cols="4">
                        <v-text-field
                            type="date"
                            label="Fecha"
                            v-model="newInvestment.fecha">
                        </v-text-field>
                        <v-text-field
                            type="number"
                            label="Monto"
                            v-model="newInvestment.monto"
                            append-icon="mdi-currency-usd">
                        </v-text-field>
                    </v-col>
                </v-row>
                <v-btn @click="save('wpt_investments', -1)">
                    <v-icon>mdi-content-save</v-icon>Agregar
                </v-btn>                
            </v-form>
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
                    <template #item.action="{item}">
                        <v-icon 
                            @click="cobrar"
                            :disabled="item.cobro>0"
                            v-if="!item.released">mdi-currency-usd</v-icon>
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