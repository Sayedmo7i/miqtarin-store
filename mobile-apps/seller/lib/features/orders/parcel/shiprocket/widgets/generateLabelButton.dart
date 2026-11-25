import 'package:eshopplus_seller/commons/widgets/customRoundedButton.dart';
import 'package:eshopplus_seller/commons/widgets/customTextContainer.dart';
import 'package:eshopplus_seller/core/localization/labelKeys.dart';
import 'package:eshopplus_seller/features/orders/parcel/models/parcel.dart';
import 'package:eshopplus_seller/features/orders/parcel/shiprocket/blocs/GenerateLabelCubit.dart';
import 'package:eshopplus_seller/features/profile/chat/blocs/downloadFileCubit.dart';
import 'package:eshopplus_seller/utils/utils.dart';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:eshopplus_seller/commons/blocs/settingsAndLanguagesCubit.dart';
import 'package:eshopplus_seller/commons/widgets/customCircularProgressIndicator.dart';

import 'package:open_file/open_file.dart';

class GenerateLabelButton extends StatelessWidget {
  final Parcel parcel;
  const GenerateLabelButton({Key? key, required this.parcel}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return MultiBlocProvider(
      providers: [
        BlocProvider(create: (context) => GenerateLabelCubit()),
        BlocProvider(create: (context) => DownloadFileCubit()),
      ],
      child: BlocConsumer<GenerateLabelCubit, GenerateLabelState>(
        listener: (context, state) {
          if (state is GenerateLabelSuccess && state.label.trim().isNotEmpty) {
            final pdfUrl = state.label;
            parcel.trackingDetails?.labelUrl = pdfUrl;

            context.read<DownloadFileCubit>().downloadFile(
              fileUrl: pdfUrl,
              fName: 'Label_${parcel.name!}.pdf',
            );
          }
          if (state is GenerateLabelFailure)
            Utils.showSnackBar(message: state.errorMessage);
        },
        builder: (context, state) {
          return BlocConsumer<DownloadFileCubit, DownloadFileState>(
            listener: (context, downloadstate) {
              if (downloadstate is DownloadFileSuccess) {
                Utils.showSnackBar(
                  message: labelDownloadedKey,
                  msgDuration: const Duration(seconds: 3),
                  action: SnackBarAction(
                    label: context
                        .read<SettingsAndLanguagesCubit>()
                        .getTranslatedValue(labelKey: viewKey),
                    textColor: Theme.of(context).colorScheme.onSecondary,
                    onPressed: () async {
                      await OpenFile.open(downloadstate.downloadedFilePath);
                    },
                  ),
                );
              } else if (downloadstate is DownloadFileFailure) {
                Utils.showSnackBar(message: downloadstate.errorMessage);
              }
            },
            builder: (context, downloadstate) {
              return Container(
                color: Theme.of(context).colorScheme.primaryContainer,
                child: CustomRoundedButton(
                  widthPercentage: 1.0,
                  buttonTitle: '',
                  showBorder: true,
                  backgroundColor: Theme.of(
                    context,
                  ).colorScheme.primaryContainer,
                  borderColor: Theme.of(
                    context,
                  ).colorScheme.secondary.withValues(alpha: 0.3),
                  style: Theme.of(context).textTheme.bodySmall!,
                  child:
                      state is GenerateLabelProgress ||
                          downloadstate is DownloadFileProcessCanceled
                      ? CustomCircularProgressIndicator()
                      : Row(
                          mainAxisAlignment: .center,
                          spacing: 4,
                          children: [
                            Icon(Icons.local_offer_outlined),
                            CustomTextContainer(
                              textKey:
                                  parcel.trackingDetails!.labelUrl != null &&
                                      parcel
                                          .trackingDetails!
                                          .labelUrl!
                                          .isNotEmpty
                                  ? downloadLabelKey
                                  : generateLabelKey,
                            ),
                          ],
                        ),
                  onTap: () {
                    if (parcel.trackingDetails!.labelUrl?.isEmpty ?? true) {
                      if (state is! GenerateLabelProgress)
                        context.read<GenerateLabelCubit>().generateLabel(
                          shipmentId: parcel.trackingDetails!.shipmentId ?? '',
                        );
                    } else {
                      context.read<DownloadFileCubit>().downloadFile(
                        fileUrl: parcel.trackingDetails!.labelUrl!,
                        fName: 'Label_${parcel.name!}.pdf',
                      );
                    }
                  },
                ),
              );
            },
          );
        },
      ),
    );
  }
}
