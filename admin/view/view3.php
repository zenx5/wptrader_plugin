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
                <v-col cols="6">
                    <b>Correo</b>
                </v-col>
                <v-col cols="6">
                    <i> {{ temp.correo }} </i>
                </v-col>
            </v-row>
            <v-row>
                <v-col cols="6">
                    <b>Pais</b>
                </v-col>
                <v-col cols="6">
                    <i> {{ temp.pais }} </i>
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
                    <v-col cols="6">
                        <v-text-field
                            label="Nombre"
                            v-model="newInvesment.fecha"
                            counter>
                        </v-text-field>
                    </v-col> 
                    <v-col cols="6">
                        <v-text-field
                            label="Apellido"
                            v-model="newInvesment.monto"
                            counter>
                        </v-text-field>
                    </v-col>
                </v-row>
                <v-btn >
                    <v-icon>mdi-content-save</v-icon>Agregar
                </v-btn>
                
            </v-form>
            <v-divider></v-divider>
            <v-row>
                <v-col cols="12">
                    <v-data-table
                        :headers="headerInvesment"
                        :items="investments | forKey('usuario',temp.id)"
                    >
                    <template #item.fecha="{item}">
                        {{ item.fecha | date }}
                    </template>
                    <template #item.fechacobro="{item}">
                        {{ item.fechacobro | date }}
                    </template>
                    <template #item.cobro="{item}">
                        <span v-if="item.cobro>0">
                            {{ item.cobro }} dias
                        </span>
                        <v-chip v-else color="#0f0">Cobrar!!</v-chip>
                    </template>
                    <template #item.action="{item}">
                        <v-icon 
                            @click="cobrar"
                            :disabled="item.cobro>0">mdi-content-save</v-icon>
                    </template>
                    </v-data-table>
                </v-col>
            </v-row>
        </v-card-text>
    
        <v-card-actions>
            <v-row>
                <v-col cols="6">
                    <v-btn @click="view">Volver</v-btn>
                    <v-btn style="background-color: blue; color:white;">Cobrar</v-btn>

                </v-col>
            </v-row>
        </v-card-actions>
            
    </v-card>
</v-tab-item>