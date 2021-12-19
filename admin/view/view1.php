<?php


?>
<v-tab-item>
    <v-card flat>
        <v-card-title> Dashboard </v-card-title>
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
                            <v-text-field 
                                counter 
                                :disabled="editRow != index" 
                                v-model="item.nombre"/>
                        </template>
                        <template #item.apellido="{item, index}">
                            <v-text-field 
                                counter 
                                :disabled="editRow != index" 
                                v-model="item.apellido" />
                            
                        </template>
                        <template #item.cedula="{item, index}">
                            <v-text-field 
                                counter 
                                :disabled="editRow != index" 
                                v-model="item.cedula" />
                        </template>
                        <template #item.correo="{item, index}">
                            <v-text-field 
                                type="correo"
                                counter 
                                :disabled="editRow != index" 
                                v-model="item.correo" />
                        </template>
                        <template #item.pais="{item, index}">
                            <v-select
                                :items="countries | forKey('enable', 1)"
                                item-text="label"
                                item-value="label"
                                label="Country"
                                v-model="item.pais"
                                :disabled="editRow != index">
                            </v-select>
                        </template>
                        <template #item.postalcode="{item, index}">
                            <v-text-field 
                                counter 
                                :disabled="editRow != index" 
                                v-model="item.postalcode" />
                        </template>
                        <template #item.telefono="{item, index}">
                            <v-text-field 
                                counter 
                                :disabled="editRow != index" 
                                v-model="item.telefono" />
                        </template>
                        <template #item.monto="{item, index}">
                            <v-chip 
                                v-if="editRow != index"
                                style="font-weight: bold; border:1px solid black;"
                                :color="getColor(item.monto)">
                                {{item.monto}}
                            </v-chip>
                            <v-text-field 
                                counter 
                                v-else
                                v-model="item.monto" />
                        </template>
                        <template #item.accion="{item, index}">
                            <span class="action">
                                <v-icon class="btn-action" @click="view(index)">mdi-eye</v-icon>
                                <v-icon class="btn-action" @click="edit(index)" v-if="editRow != index">mdi-pencil</v-icon>
                                <v-icon class="btn-action" @click="save('wpt_users', item.id)" v-else>mdi-content-save</v-icon>
                                <v-icon class="btn-action" @click="del('wpt_users', item.id)">mdi-delete</v-icon>
                            </span>
                        </template>
                    </v-data-table>
                    
                </v-col>
            </v-row>
        </v-card-text>
        <v-card-text>
            <v-card-title>Nuevo Inversor </v-card-title>
            <v-form>
                <v-row>
                    <v-col col="3">
                        <v-text-field 
                            label="Nombre"
                            v-model="temp.nombre"
                            counter>
                        </v-text-field>                    
                    </v-col>
                    <v-col col="3">
                        <v-text-field
                            label="Apellido"
                            v-model="temp.apellido"
                            counter>
                        </v-text-field>                    
                    </v-col>
                    <v-col col="3">
                        <v-text-field
                            label="Cedula"
                            v-model="temp.cedula"
                            counter>
                        </v-text-field>                    
                    </v-col>
                    <v-col col="3">
                        <v-text-field
                            type="email"
                            label="Correo"
                            v-model="temp.correo"
                            counter>
                        </v-text-field>                    
                    </v-col>
                </v-row>
                <v-row>
                    <v-col cols="3">
                        <v-select
                            :items="countries | forKey('enable', 1)"
                            item-text="label"
                            item-value="label"
                            label="Pais"
                            v-model="temp.pais">
                        </v-select>
                    </v-col>
                    <v-col cols="2">
                        <v-text-field
                            label="Codigo Postal"
                            v-model="temp.postalcode"
                            counter>
                        </v-text-field>
                    </v-col>
                    <v-col cols="2">
                        <v-text-field
                            label="Telefono"
                            v-model="temp.telefono"
                            counter>
                        </v-text-field>
                    </v-col>
                    <v-col>
                        <v-select
                            :items="$userswp"
                            item-text="data.display_name"
                            item-value="data.ID"
                            label="Usuario WordPress"
                        >
                        </v-select>
                    </v-col>
                    <v-col>
                        <v-btn 
                            @click="save('wpt_users',-1)"
                            style="margin-top: 10px">
                            <v-icon>mdi-content-save</v-icon>Guardar
                        </v-btn>
                    </v-col>
                </v-row>
            </v-form>
        </v-card-text>
    </v-card>
</v-tab-item>

