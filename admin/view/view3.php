<?php


?>
<v-tab-item >
    <v-card flat>
        <v-card-title > {{ temp.nombre }} {{ temp.apellido }} </v-card-title>
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