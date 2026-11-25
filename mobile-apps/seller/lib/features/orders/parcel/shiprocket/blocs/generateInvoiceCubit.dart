import 'package:eshopplus_seller/features/orders/parcel/shiprocket/repositories/shiprocketRepository.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

abstract class GenerateInvoiceState {}

class GenerateInvoiceInitial extends GenerateInvoiceState {}

class GenerateInvoiceProgress extends GenerateInvoiceState {}

class GenerateInvoiceFailure extends GenerateInvoiceState {
  final String errorMessage;
  GenerateInvoiceFailure(this.errorMessage);
}

class GenerateInvoiceSuccess extends GenerateInvoiceState {
  final String invoiceUrl;

  GenerateInvoiceSuccess({required this.invoiceUrl});
}

class GenerateInvoiceCubit extends Cubit<GenerateInvoiceState> {
  final ShiprocketRepository shiprocketRepository = ShiprocketRepository();
  GenerateInvoiceCubit() : super(GenerateInvoiceInitial());
  void generateInvoice({required String shiprocketId}) async {
    emit(GenerateInvoiceProgress());

    try {
      final result = await shiprocketRepository.generateInvoice(
        shiprocketId: shiprocketId,
      );

      emit(GenerateInvoiceSuccess(invoiceUrl: result));
    } catch (e) {
      emit(GenerateInvoiceFailure(e.toString()));
    }
  }
}
