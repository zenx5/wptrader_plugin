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
                        <template #item.id="{item, index}">
                            <span v-if="editRow != index">
                                <div>{{item.id}}</div>
                                <div style="border-top: 1px solid black"><span v-if="item.wpid!=-1">wp: <b>{{item.wpid}}</b></span><span v-else>no asignado</span></div>
                            </span>
                            <span v-else>
                            <v-select
                                :items="$userswp"
                                item-text="data.display_name"
                                item-value="data.ID"
                                v-model="item.wpid"
                                label="Usuario  WordPress"
                            >
                            </v-select>
                            </span>
                        </template>
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
                    <v-col>
                        <v-select
                            :items="$userswp | notUsed(users)"
                            item-text="data.display_name"
                            item-value="data.ID"
                            v-model="temp.wpid"
                            label="Usuario WordPress"
                        >
                        </v-select>
                    </v-col>
                    <v-col>
                        <v-btn 
                            @click="saveWithWP"
                            style="margin-top: 10px">
                            <v-icon>mdi-content-save</v-icon>Guardar
                        </v-btn>
                    </v-col>
                </v-row>
            </v-form>
        </v-card-text>
    </v-card>
</v-tab-item>

