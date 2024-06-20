<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

const asset = (path: any) => `/storage/${path}`;
</script>

<template>        
	<div class="w-full min-h-screen bg-red-900 flex flex-col items-center">
		<div class="base-red w-11/12 mt-3 mb-3">
			<div class="flex flex-col items-center">
                <div class="flex flex-row justify-between w-full">
                    <div class="flex items-center margin-left-dragon">
                        <img :src="asset('pictures/dragon-small.png')" alt="Dragon" class="small-dragon">
                        <div class="chinese-text">
                            {{ $t('messages.The Golden Dragon') }}
                        </div>
                        <img :src="asset('pictures/dragon-small.png')" alt="Dragon" class="small-dragon inverted">
                    </div>

					<div class="scroll-container flex ml-auto">
						<Link class="scroll-text" :href="route('welcome')">
							{{ $t('messages.Welcome to The Golden Dragon. Click on this text to see this week\'s specials!') }}
						</Link>
					</div>

                    <div class="flex items-center margin-right-dragon">
                        <img :src="asset('pictures/dragon-small.png')" alt="Dragon" class="small-dragon">
                        <div class="chinese-text">
                            {{ $t('messages.The Golden Dragon') }}
                        </div>
                        <img :src="asset('pictures/dragon-small.png')" alt="Dragon" class="small-dragon inverted">
                    </div>
                </div>
			</div>
			<table class="w-full" id="main_table" style="padding:5px;border-collapse: collapse">
				<tr style="height:7px;background-color:red">
					<td colspan="9">
					</td>
				</tr>
				<tr style="height:25px;background-color:red">
				<td width="7px">
					</td>
					<td style="width:25px;border-left:4px solid yellow;border-top:4px solid yellow"></td>
					<td style="width:25px;border-right:4px solid yellow;border-top:4px solid yellow"></td>
					<td style="width:25px;border-right:4px solid yellow;border-bottom:4px solid yellow"></td>
					<td style="border-top:4px solid yellow;border-bottom:4px solid yellow"></td>
					<td style="width:25px;border-left:4px solid yellow;border-bottom:4px solid yellow"></td>
					<td style="width:25px;border-left:4px solid yellow;border-top:4px solid yellow"></td>
					<td style="width:25px;border-right:4px solid yellow;border-top:4px solid yellow"></td>
					<td width="7px"></td>
				</tr>
				<tr style="height:25px;background-color:red">
					<td width="7px"></td>
					<td style="width:25px;border-left:4px solid yellow;border-bottom:4px solid yellow"></td>
					<td style="width:25px;border:4px solid yellow"></td>
					<td style="width:25px;border:4px solid yellow"></td>
					<td></td>
					<td style="width:25px;border:4px solid yellow"></td>
					<td style="width:25px;border:4px solid yellow"></td>
					<td style="width:25px;border-right:4px solid yellow;border-bottom:4px solid yellow"></td>
					<td width="7px"></td>
				</tr>
				<tr style="height:25px;background-color:red">
					<td width="7px"></td>
					<td style="width:25px;border-right:4px solid yellow;border-bottom:4px solid yellow"></td>
					<td style="width:25px;border:4px solid yellow"></td>
					<td style="width:25px"></td>
					<td></td>
					<td style="width:25px"></td>
					<td style="width:25px;border:4px solid yellow"></td>
					<td style="width:25px;border-bottom:4px solid yellow"></td>
					<td width="7px"></td>
				</tr>
				<tr style="height:50px;background-color:red"> 
					<td width="7px"></td>
					<td style="width:25px;border-right:4px solid yellow;border-left:4px solid yellow"></td>
					<td style="width:25px;"></td>
					<td style="width:25px;"></td>
					<td style="text-align:center">
						<slot />
					</td>
					<td style="width:25px;"></td>
					<td style="width:25px;"></td>
					<td style="width:25px;border-right:4px solid yellow;border-left:4px solid yellow"></td>
					<td width="7px"></td>
				</tr>
				<tr style="height:25px;background-color:red">
					<td width="7px"></td>
					<td style="width:25px;border-right:4px solid yellow;border-top:4px solid yellow"></td>
					<td style="width:25px;border:4px solid yellow"></td>
					<td style="width:25px"></td>
					<td></td>
					<td style="width:25px"></td>
					<td style="width:25px;border:4px solid yellow"></td>
					<td style="width:25px;border-top:4px solid yellow"></td>
					<td width="7px"></td>
				</tr>
				<tr style="height:25px;background-color:red">
					<td width="7px"></td>
					<td style="width:25px;border-left:4px solid yellow;border-top:4px solid yellow"></td>
					<td style="width:25px;border:4px solid yellow"></td>
					<td style="width:25px;border:4px solid yellow"></td>
					<td></td>
					<td style="width:25px;border:4px solid yellow"></td>
					<td style="width:25px;border:4px solid yellow"></td>
					<td style="width:25px;border-right:4px solid yellow;border-top:4px solid yellow"></td>
					<td width="7px"></td>
				</tr>
				<tr style="height:25px;background-color:red">
					<td width="7px"></td>
					<td style="width:25px;border-left:4px solid yellow;border-bottom:4px solid yellow"></td>
					<td style="width:25px;border-right:4px solid yellow;border-bottom:4px solid yellow"></td>
					<td style="width:25px;border-right:4px solid yellow"></td>
					<td style="border-top:4px solid yellow;border-bottom:4px solid yellow"></td>
					<td style="width:25px;border-left:4px solid yellow;"></td>
					<td style="width:25px;border-left:4px solid yellow;border-bottom:4px solid yellow"></td>
					<td style="width:25px;border-right:4px solid yellow;border-bottom:4px solid yellow"></td>
					<td width="7px"></td>
				</tr>
				<tr style="height:7px;background-color:red">
					<td colspan="9">
					</td>
				</tr>
			</table>
		</div>
	</div>
</template>