<?php


?>
<v-tab-item>
    <v-card flat>
        <v-card-title> Dashboard <?=WP_Trader::get('db_created')?> </v-card-title>
        <v-card-subtitle> Dashboard </v-card-subtitle>
        <v-card-text>
            <v-row>
                <v-col>
                    <v-data-table 
                        :headers="headers"
                        :items="users"
                        style="text-align:center;"
                        >
                        <template #item.nombre="{item, index}">
                            <span v-if="editRow != index">{{item.nombre}}</span>
                            <v-text-field counter v-else :value="item.nombre" />
                        </template>
                        <template #item.apellido="{item, index}">
                            <span v-if="editRow != index">{{item.apellido}}</span>
                            <v-text-field type="text" :value="item.apellido" v-else />
                        </template>
                        <template #item.cedula="{item, index}">
                            <span v-if="editRow != index">{{item.cedula}}</span>
                            <v-text-field type="text" :value="item.cedula" v-else />
                        </template>
                        <template #item.correo="{item, index}">
                            <span v-if="editRow != index">{{item.correo}}</span>
                            <v-text-field type="email" :value="item.correo" v-else />
                        </template>
                        
                        <template #item.accion="{item, index}">
                            <span class="action">
                                <v-icon class="btn-action" @click="view">mdi-eye</v-icon>
                                <v-icon class="btn-action" @click="edit(index)" v-if="editRow != index">mdi-pencil</v-icon>
                                <v-icon class="btn-action" @click="save(index)" v-else>mdi-content-save</v-icon>
                                <v-icon class="btn-action" @click="del">mdi-delete</v-icon>
                            </span>
                        </template>
                    </v-data-table>
                    
                </v-col>
            </v-row>
        </v-card-text>
    </v-card>
</v-tab-item>

