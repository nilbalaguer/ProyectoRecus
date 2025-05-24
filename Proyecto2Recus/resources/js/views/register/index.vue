<template>
    <div class="d-flex justify-content-start">

        <div class="w-50 vh-100 d-flex justify-content-center align-items-center d-none d-md-flex">
            <div class="w-75 h-100 m-auto advertisment-menu" style="background-color: var(--background4);">
                <h2 class="brandName w-100 text-left pt-5">{{ $t('brandName') }}.</h2>
            </div>
        </div>

        <div class="w-100 w-md-50 loginContainer">
            <div class="container">
                <div class="row justify-content-center my-4">
                    <div class="col-md-7">
                        <div class="card border-0 shadow-sm p-3">
                            <div class="card-body iniciosession">
                                <h2 class="mb-4">{{ $t('register') }}</h2>
                                <form @submit.prevent="submitRegister">
                                    
                                    <!-- Nombre -->
                                    <div class="mb-3 w-100 m-auto name-and-username-register">
                                        <div>
                                            <label for="name" class="form-label">{{ $t('name') }}</label>
                                            <br>
                                            <input placeholder="Jhonatan Ortega" v-model="registerForm.name" id="name" type="text" class="form-registro text-left form-control form-control-sm" autofocus>
                                            <div v-if="validationErrors?.name" class="text-danger mt-1">
                                                <div v-for="msg in validationErrors.name" :key="msg">{{ msg }}</div>
                                            </div>
                                        </div>

                                        <!-- Username -->
                                        <div>
                                            <label for="username" class="form-label">{{ $t('username') }}</label>
                                            <br>
                                            <input placeholder="jhonatan_ortega" v-model="registerForm.username" id="username" type="text" class="form-registro text-left form-control form-control-sm" autocomplete="username">
                                            <div v-if="validationErrors?.username" class="text-danger mt-1">
                                                <div v-for="msg in validationErrors.username" :key="msg">{{ msg }}</div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Email -->
                                    <div class="mb-3 w-100">
                                        <label for="email" class="form-label text-center campo-email-registro">{{ $t('email') }}</label>
                                        <input placeholder="jhonatan@email.com" v-model="registerForm.email" id="email" type="email" class="form-registro text-center form-control form-control-sm" autocomplete="email">
                                        <div v-if="validationErrors?.email" class="text-danger mt-1">
                                            <div v-for="msg in validationErrors.email" :key="msg">{{ msg }}</div>
                                        </div>
                                    </div>

                                    <!-- Contraseña -->
                                    <div class="mb-3 w-100 m-auto name-and-username-register">
                                        <div>
                                            <label for="password" class="form-label">{{ $t('password') }}</label>
                                            <input placeholder="**********" v-model="registerForm.password" id="password" type="password" class="form-registro form-control form-control-sm text-left" autocomplete="new-password">
                                            <div v-if="validationErrors?.password" class="text-danger mt-1">
                                                <div v-for="msg in validationErrors.password" :key="msg">{{ msg }}</div>
                                            </div>
                                        </div>

                                        <!-- Confirmar contraseña -->
                                        <div>
                                            <label for="password_confirmation" class="form-label">{{ $t('confirm_password') }}</label>
                                            <input placeholder="**********" v-model="registerForm.password_confirmation" id="password_confirmation" type="password" class="form-registro form-control form-control-sm text-left" autocomplete="new-password">
                                            <div v-if="validationErrors?.password_confirmation" class="text-danger mt-1">
                                                <div v-for="msg in validationErrors.password_confirmation" :key="msg">{{ msg }}</div>
                                            </div>
                                        </div>
                                    </div>
            
                                    <!-- Términos -->
                                    <div class="form-check mb-3 w-100 m-auto">
                                        <input class="form-check-input checkInput" type="checkbox" name="terms" id="terms" required>
                                        <label class="form-check-label checkLabel" for="terms">
                                            {{ $t('termsAndConditions') }}
                                        </label>
                                    </div>

                                    <!-- Botón -->
                                    <div class="d-flex justify-content-end mt-3">
                                        <button class="btn btn-primary btn-sm primary-button" :class="{ 'opacity-25': processing }" :disabled="processing">
                                            <span v-if="processing" class="spinner-border spinner-border-sm"></span>
                                            {{ $t('register') }}
                                        </button>
                                    </div>

                                    <router-link :to="{name: 'auth.login'}">{{ $t('already_registered') }}</router-link>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script setup>
import useAuth from '@/composables/auth'

const { registerForm, validationErrors, processing, submitRegister } = useAuth();
</script>

<style>
.form-label
{
    width: 100%;
    text-align: left;
}
</style>