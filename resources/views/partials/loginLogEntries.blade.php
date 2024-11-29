                                     @foreach($log as $logs)
                                    <tr class="text-sm border-t">
                                        <td class="py-2 pl-3 flex gap-2 items-center"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 20 20" fill="none">
                                            <circle cx="10" cy="10" r="10" fill="#D9D9D9"/>
                                          </svg> {{ $logs->user->name }}</td>
                                        <td class="py-2 pl-3"> {{ \Carbon\Carbon::parse($logs->date)->format('d-m-y') }}</td>
                                        <td class="py-2 pl-3">{{ \Carbon\Carbon::parse($logs->date)->format('H:i:s') }}</td>
                                        
                                        <td class="py-2 pl-3"><span class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" class="mr-2"
                                                viewBox="0 0 18 18" fill="none">
                                                <g clip-path="url(#clip0_527_173)">
                                                    <path
                                                        d="M17.7188 0.22998L8.29691 1.61289V8.62037L17.7188 8.54528V0.22998ZM0.231644 9.38073L0.232065 15.3438L7.37455 16.3258L7.36893 9.42714L0.231644 9.38073ZM8.22196 9.47636L8.23518 16.4324L17.7099 17.7696L17.7122 9.49196L8.22196 9.47636ZM0.22644 2.71342L0.23305 8.67353L7.37553 8.63289L7.3723 1.74001L0.22644 2.71342Z"
                                                        fill="#00ADEF" />
                                                </g>
                                                <defs>
                                                    <clipPath id="clip0_527_173">
                                                        <rect width="18" height="18" fill="white" />
                                                    </clipPath>
                                                </defs>
                                            </svg>
                                            {{ $logs->system . ' ' . $logs->system_version }}
                                        </span></td>
                                        <td class="py-2 pl-3">
                                            <span class="flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" class="mr-2"
                                                    viewBox="0 0 18 18" fill="none">
                                                    <g clip-path="url(#clip0_491_188)">
                                                        <path
                                                            d="M8.99998 14.0074C11.7657 14.0074 14.0077 11.7653 14.0077 8.99957C14.0077 6.23382 11.7657 3.9917 8.99998 3.9917C6.23424 3.9917 3.99219 6.23382 3.99219 8.99957C3.99219 11.7653 6.23424 14.0074 8.99998 14.0074Z"
                                                            fill="white" />
                                                        <path
                                                            d="M2.5234 6.53891C2.14983 5.89182 1.71073 5.21268 1.20603 4.50146C0.415977 5.86956 2.68367e-05 7.42155 1.29856e-09 9.00138C-2.68341e-05 10.5812 0.41587 12.1332 1.20588 13.5013C1.99588 14.8695 3.13215 16.0055 4.50045 16.7952C5.86874 17.5849 7.42083 18.0005 9.00066 18.0001C9.82894 16.8384 10.3914 16.0007 10.6879 15.4872C11.2573 14.5008 11.9938 13.0887 12.8973 11.2508V11.2497C12.5026 11.9341 11.9346 12.5024 11.2505 12.8977C10.5665 13.2929 9.79038 13.501 9.00034 13.5011C8.2103 13.5013 7.43415 13.2934 6.74995 12.8984C6.06575 12.5033 5.49761 11.9351 5.10268 11.2509C3.87551 8.96237 3.0158 7.39166 2.5234 6.53891Z"
                                                            fill="#229342" />
                                                        <path
                                                            d="M8.99966 17.9998C10.1816 18 11.352 17.7674 12.4439 17.3151C13.5359 16.8629 14.5281 16.2 15.3638 15.3642C16.1995 14.5284 16.8624 13.5362 17.3146 12.4442C17.7667 11.3522 17.9994 10.1818 17.9991 8.99991C17.9988 7.42005 17.5826 5.86811 16.7923 4.50012C15.0874 4.33207 13.8291 4.24805 13.0175 4.24805C12.0973 4.24805 10.7578 4.33207 8.99903 4.50012L8.99805 4.50082C9.7881 4.50045 10.5643 4.70811 11.2487 5.10292C11.933 5.49773 12.5013 6.06577 12.8965 6.74991C13.2916 7.43407 13.4996 8.2102 13.4996 9.00027C13.4996 9.79033 13.2915 10.5665 12.8964 11.2506L8.99966 17.9998Z"
                                                            fill="#FBC116" />
                                                        <path
                                                            d="M9.00023 12.5635C10.9679 12.5635 12.5629 10.9684 12.5629 9.00072C12.5629 7.03296 10.9679 5.43799 9.00016 5.43799C7.03261 5.43799 5.4375 7.0331 5.4375 9.00072C5.4375 10.9683 7.03261 12.5635 9.00023 12.5635Z"
                                                            fill="#1A73E8" />
                                                        <path
                                                            d="M9.00103 4.50031H16.7943C16.0046 3.13199 14.8685 1.99571 13.5004 1.20573C12.1322 0.41574 10.5802 -0.000106877 9.00034 2.06043e-08C7.42049 0.000106919 5.86852 0.416164 4.50048 1.20634C3.13244 1.99651 1.99655 3.13294 1.20703 4.50136L5.10368 11.2507L5.10473 11.2512C4.7094 10.5672 4.50115 9.79112 4.50092 9.00107C4.50069 8.21101 4.70848 7.43482 5.10341 6.75056C5.49834 6.06629 6.06647 5.49808 6.75068 5.10305C7.43489 4.70803 8.21105 4.50012 9.0011 4.50024L9.00103 4.50031Z"
                                                            fill="#E33B2E" />
                                                    </g>
                                                    <defs>
                                                        <clipPath id="clip0_491_188">
                                                            <rect width="18" height="18" fill="white" />
                                                        </clipPath>
                                                    </defs>
                                                </svg>
                                                 {{ $logs->browser }}
                                            </span>
    
                                        </td>
                                        <td class="py-2 pl-3 pr-2">
                                            India
                                        </td>
                                    </tr>
                                   
                                    
                                    @endforeach